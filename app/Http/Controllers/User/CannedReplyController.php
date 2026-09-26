<?php

namespace App\Http\Controllers\User;

use DB;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\StoreAutoReply;
use App\Models\Addon;
use App\Models\AutoReply;
use App\Models\Setting;
use App\Services\AutoReplyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CannedReplyController extends BaseController
{
    private $autoReplyService;

    public function __construct(AutoReplyService $autoReplyService)
    {
        $this->autoReplyService = $autoReplyService;
    }

    public function index(Request $request){
        $rows = $this->autoReplyService->getRows($request);
        $aiAssistantAddon = Addon::where('name', 'AI Assistant')->first();
        $aiAssistantSetting = Setting::where('key', 'ai_assistant')->first();
        $flowBuilderAddon = Addon::where('name', 'Flow builder')->first();
        $flowBuilderSetting = Setting::where('key', 'flow_builder')->first();

        $aimodule = $aiAssistantAddon && $aiAssistantAddon->status && $aiAssistantSetting && $aiAssistantSetting->value == 1;
        $fbmodule = $flowBuilderAddon && $flowBuilderAddon->status && $flowBuilderSetting && $flowBuilderSetting->value == 1;

        return Inertia::render('User/Automation/Basic/Index', [ 
            'title' => __('Canned replies'), 
            'allowCreate' => true, 
            'rows' => $rows, 
            'filters' => request()->all(), 
            'aimodule' => $aimodule,
            'fbmodule' => $fbmodule,
        ]);
    }

    public function create(){
        $data['title'] = __('Canned replies');
        $placeholders = config('formats.placeholders');
        $organizationId = session()->get('current_organization');
        $additionalFields = DB::table('contact_fields')
            ->where('organization_id', $organizationId)
            ->where('deleted_at', null)
            ->pluck('name');

        $additionalPlaceholders = $additionalFields->map(function($name) {
            // Convert name to lowercase and replace spaces with underscores
            $value = '{' . strtolower(str_replace(' ', '_', $name)) . '}';
            return [
                'value' => $value,
                'label' => $name,
            ];
        })->toArray();

        $data['placeholders'] = array_merge($placeholders, $additionalPlaceholders);

        return Inertia::render('User/Automation/Basic/Create', $data);
    }

    public function store(StoreAutoReply $request){
        $this->autoReplyService->store($request);

        return Redirect::route('cannedReply.create')->with(
            'status', [
                'type' => 'success', 
                'message' => __('Data added successfully!')
            ]
        );
    }

    public function edit($uuid){
        $organizationId = session()->get('current_organization');
        $data['autoreply'] = AutoReply::where('uuid', $uuid)
            ->where('organization_id', $organizationId)
            ->whereNull('deleted_at')
            ->firstOrFail();
        $placeholders = config('formats.placeholders');
        $additionalFields = DB::table('contact_fields')
            ->where('organization_id', $organizationId)
            ->where('deleted_at', null)
            ->pluck('name');

        $additionalPlaceholders = $additionalFields->map(function($name) {
            // Convert name to lowercase and replace spaces with underscores
            $value = '{' . strtolower(str_replace(' ', '_', $name)) . '}';
            return [
                'value' => $value,
                'label' => $name,
            ];
        })->toArray();

        $data['placeholders'] = array_merge($placeholders, $additionalPlaceholders);

        return Inertia::render('User/Automation/Basic/Edit', $data);
    }

    public function update(StoreAutoReply $request, $uuid){
        $this->autoReplyService->store($request, $uuid);

        return Redirect::route('cannedReply.edit', $uuid)->with(
            'status', [
                'type' => 'success', 
                'message' => __('Data updated successfully!')
            ]
        );
    }

    public function delete($uuid)
    {
        $this->autoReplyService->destroy($uuid);

        return Redirect::back()->with(
            'status', [
                'type' => 'success', 
                'message' => __('Row deleted successfully!')
            ]
        );
    }

    public function builder($uuid)
    {
        $organizationId = session()->get('current_organization');
        $autoreply = AutoReply::where('uuid', $uuid)
            ->where('organization_id', $organizationId)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $placeholders = config('formats.placeholders');
        $additionalFields = DB::table('contact_fields')
            ->where('organization_id', $organizationId)
            ->whereNull('deleted_at')
            ->pluck('name');

        $additionalPlaceholders = $additionalFields->map(function($name) {
            $value = '{' . strtolower(str_replace(' ', '_', $name)) . '}';
            return [
                'value' => $value,
                'label' => $name,
            ];
        })->toArray();

        $templates = DB::table('templates')
            ->where('organization_id', $organizationId)
            ->whereNull('deleted_at')
            ->select('id', 'uuid', 'name', 'type', 'category', 'status', 'metadata')
            ->get();

        $contacts = DB::table('contacts')
            ->where('organization_id', $organizationId)
            ->whereNull('deleted_at')
            ->select('id', 'uuid', 'first_name', 'last_name', 'full_name', 'phone', 'email')
            ->limit(20)
            ->get();

        return Inertia::render('User/Automation/Builder/Index', [
            'title' => __('Workflow Builder'),
            'autoreply' => $autoreply,
            'placeholders' => array_merge($placeholders, $additionalPlaceholders),
            'templates' => $templates,
            'contacts' => $contacts,
        ]);
    }

    public function saveWorkflow(Request $request, $uuid)
    {
        $organizationId = session()->get('current_organization');
        $autoreply = AutoReply::where('uuid', $uuid)
            ->where('organization_id', $organizationId)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $metadata = json_decode($autoreply->metadata ?? '{}', true) ?: [];

        if ($request->has('name') && $request->filled('name')) {
            $autoreply->name = $request->name;
        }

        if ($request->has('trigger') && $request->filled('trigger')) {
            $autoreply->trigger = $request->trigger;
        }

        if ($request->has('match_criteria') && $request->filled('match_criteria')) {
            $autoreply->match_criteria = $request->match_criteria;
        }

        if ($request->has('response_type')) {
            $metadata['type'] = $request->response_type;
            if ($request->response_type === 'text') {
                $metadata['data']['text'] = $request->response ?? '';
            } elseif ($request->response_type === 'template') {
                $metadata['data']['template'] = $request->response ?? '';
            }
        }

        if ($request->has('description')) {
            $metadata['description'] = $request->description;
        }

        if ($request->has('status')) {
            $metadata['status'] = $request->status;
        }

        if ($request->has('workflow')) {
            $metadata['workflow'] = $request->workflow;
        }

        $autoreply->metadata = json_encode($metadata);
        $autoreply->updated_at = now();
        $autoreply->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Workflow saved successfully!'),
                'autoreply' => $autoreply,
            ]);
        }

        return Redirect::back()->with('status', [
            'type' => 'success',
            'message' => __('Workflow saved successfully!'),
        ]);
    }

    public function toggleStatus(Request $request, $uuid)
    {
        $organizationId = session()->get('current_organization');
        $autoreply = AutoReply::where('uuid', $uuid)
            ->where('organization_id', $organizationId)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $metadata = json_decode($autoreply->metadata ?? '{}', true) ?: [];
        $currentStatus = $metadata['status'] ?? 'active';
        $newStatus = $request->input('status', ($currentStatus === 'active' ? 'inactive' : 'active'));

        $metadata['status'] = $newStatus;
        $autoreply->metadata = json_encode($metadata);
        $autoreply->updated_at = now();
        $autoreply->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => __('Automation status updated to :status', ['status' => $newStatus]),
            ]);
        }

        return Redirect::back()->with('status', [
            'type' => 'success',
            'message' => __('Automation status updated!'),
        ]);
    }

    public function duplicate($uuid)
    {
        $organizationId = session()->get('current_organization');
        $original = AutoReply::where('uuid', $uuid)
            ->where('organization_id', $organizationId)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $new = $original->replicate();
        $new->uuid = (string) \Illuminate\Support\Str::uuid();
        $new->name = $original->name . ' ' . __('(Copy)');
        
        $metadata = json_decode($original->metadata ?? '{}', true) ?: [];
        $metadata['status'] = 'draft';
        $new->metadata = json_encode($metadata);
        $new->created_by = auth()->user()->id;
        $new->created_at = now();
        $new->updated_at = now();
        $new->save();

        return Redirect::back()->with('status', [
            'type' => 'success',
            'message' => __('Automation duplicated successfully as draft!'),
        ]);
    }

    public function testWorkflow(Request $request)
    {
        $organizationId = session()->get('current_organization');
        $inputMessage = $request->input('message', '');
        $trigger = $request->input('trigger', '');
        $criteria = $request->input('match_criteria', 'contains');
        $responseType = $request->input('response_type', 'text');
        $rawResponse = $request->input('response', '');
        $contactId = $request->input('contact_id');

        $contact = null;
        if ($contactId) {
            $contact = DB::table('contacts')->where('id', $contactId)->where('organization_id', $organizationId)->first();
        }

        // Test matching
        $normalizedTrigger = strtolower(trim($trigger));
        $receivedMessage = " " . strtolower($inputMessage);
        $isMatched = false;

        $triggerWords = is_string($trigger) && strpos($trigger, ',') !== false ? explode(',', $trigger) : (array)$trigger;

        foreach ($triggerWords as $trig) {
            $trig = strtolower(trim($trig));
            if (empty($trig)) continue;

            if ($criteria === 'exact match') {
                if ($receivedMessage === " " . $trig) {
                    $isMatched = true;
                    break;
                }
            } else {
                $words = explode(' ', $trig);
                $pattern = '/\b(' . implode('|', array_map('preg_quote', $words)) . ')\b/i';
                if (preg_match($pattern, $receivedMessage) === 1) {
                    $isMatched = true;
                    break;
                }
            }
        }

        // Variable replacement
        $simulatedText = $rawResponse;
        if ($contact) {
            $placeholders = [
                'first_name' => $contact->first_name ?? 'Alex',
                'last_name' => $contact->last_name ?? 'Smith',
                'full_name' => $contact->full_name ?? ($contact->first_name ? $contact->first_name . ' ' . $contact->last_name : 'Alex Smith'),
                'email' => $contact->email ?? 'alex@example.com',
                'phone' => $contact->phone ?? '+1234567890',
            ];
            foreach ($placeholders as $k => $v) {
                $simulatedText = str_replace('{' . $k . '}', $v, $simulatedText);
            }
        } else {
            $defaultReplacements = [
                '{first_name}' => 'Alex',
                '{last_name}' => 'Smith',
                '{full_name}' => 'Alex Smith',
                '{email}' => 'alex@example.com',
                '{phone}' => '+1234567890',
            ];
            foreach ($defaultReplacements as $k => $v) {
                $simulatedText = str_replace($k, $v, $simulatedText);
            }
        }

        return response()->json([
            'matched' => $isMatched,
            'input_message' => $inputMessage,
            'criteria' => $criteria,
            'trigger' => $trigger,
            'response_type' => $responseType,
            'simulated_output' => $simulatedText,
            'contact' => $contact ? [
                'name' => $contact->full_name ?? $contact->first_name,
                'phone' => $contact->phone,
            ] : null,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}