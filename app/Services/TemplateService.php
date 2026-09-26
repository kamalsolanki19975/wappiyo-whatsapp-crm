<?php

namespace App\Services;

use App\Events\NewChatEvent;
use App\Http\Resources\TemplateResource;
use App\Models\Organization;
use App\Models\Template;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use DB;
use Validator;

class TemplateService
{
    private $whatsappService;
    private $organizationId;

    public function __construct($organizationId)
    {
        $this->organizationId = $organizationId;
        $this->initializeWhatsappService();
    }

    private function initializeWhatsappService()
    {
        $config = Organization::where('id', $this->organizationId)->first()->metadata;
        $config = $config ? json_decode($config, true) : [];

        $accessToken = $config['whatsapp']['access_token'] ?? null;
        $apiVersion = config('graph.api_version');
        $appId = $config['whatsapp']['app_id'] ?? null;
        $phoneNumberId = $config['whatsapp']['phone_number_id'] ?? null;
        $wabaId = $config['whatsapp']['waba_id'] ?? null;

        $this->whatsappService = new WhatsappService($accessToken, $apiVersion, $appId, $phoneNumberId, $wabaId, $this->organizationId);
    }

    public function getTemplates(Request $request, $uuid = null, $searchTerm = null)
    {
        $response = [];

        if ($uuid === null) {
            $response = $this->getTemplatesListResponse($request);
        } elseif ($uuid === 'sync') {
            $response = $this->whatsappService->syncTemplates();
        } else {
            $response = $this->getTemplateDetailResponse($request, $uuid);
        }

        return $response;
    }

    private function getTemplatesListResponse(Request $request)
    {
        if ($request->expectsJson()) {
            $rows = Template::where('organization_id', $this->organizationId)->where('deleted_at', null)
                ->get()
                ->map(function ($row) {
                    return [
                        'value' => $row->id,
                        'label' => $row->name,
                    ];
                });

            return response()->json([$rows]);
        }

        $query = Template::where('organization_id', $this->organizationId)->where('deleted_at', null);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('language', 'like', "%{$search}%")
                  ->orWhere('metadata', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status') && $request->query('status') !== 'ALL') {
            $query->where('status', strtoupper($request->query('status')));
        }

        // Category filter
        if ($request->filled('category') && $request->query('category') !== 'ALL') {
            $query->where('category', strtoupper($request->query('category')));
        }

        // Language filter
        if ($request->filled('language') && $request->query('language') !== 'ALL') {
            $query->where('language', $request->query('language'));
        }

        // Real stats from database
        $stats = [
            'total' => Template::where('organization_id', $this->organizationId)->where('deleted_at', null)->count(),
            'approved' => Template::where('organization_id', $this->organizationId)->where('deleted_at', null)->where('status', 'APPROVED')->count(),
            'pending' => Template::where('organization_id', $this->organizationId)->where('deleted_at', null)->where('status', 'PENDING')->count(),
            'rejected' => Template::where('organization_id', $this->organizationId)->where('deleted_at', null)->where('status', 'REJECTED')->count(),
        ];

        return Inertia::render('User/Templates/Index', [
            'title' => __('templates'),
            'allowCreate' => true,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status', 'category', 'language']),
            'rows' => TemplateResource::collection(
                $query->latest()->paginate(10)->withQueryString()
            ),
        ]);
    }

    private function getTemplateDetailResponse(Request $request, $uuid)
    {
        if ($request->expectsJson()) {
            $row = Template::where('uuid', $uuid)->where('organization_id', $this->organizationId)->where('deleted_at', null)->first();
            return response()->json($row);
        }

        $data['languages'] = config('languages');
        $data['template'] = Template::where('uuid', $uuid)->where('organization_id', $this->organizationId)->whereNull('deleted_at')->firstOrFail();
        $data['title'] = 'Edit Template';
        return Inertia::render('User/Templates/Edit', $data);
    }

    public function createTemplate(Request $request)
    {
        if ($request->isMethod('get')){
            $data['languages'] = config('languages');
            $data['settings'] = Organization::where('id', $this->organizationId)->first();

            if ($request->filled('duplicate')) {
                $source = Template::where('uuid', $request->query('duplicate'))
                    ->where('organization_id', $this->organizationId)
                    ->first();
                if ($source) {
                    $data['duplicateTemplate'] = $source;
                }
            }
            
            return Inertia::render('User/Templates/Add', $data);
        } else if ($request->isMethod('post')){
            $validator = Validator::make($request->all(),[
                'name' => 'required',
                'category' => 'required',
                'language' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false,'message'=>'Some required fields have not been filled','errors'=>$validator->messages()->get('*')]);
            }

            return $this->whatsappService->createTemplate($request);
        }
    }

    public function updateTemplate(Request $request, $uuid)
    {
        $template = Template::where('uuid', $uuid)->where('organization_id', $this->organizationId)->first();
        if (!$template) {
            return response()->json(['success' => false, 'message' => __('Template not found')]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category' => 'required',
            'language' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => __('Some required fields have not been filled'), 'errors' => $validator->messages()->get('*')]);
        }

        $metadata = $template->metadata ? json_decode($template->metadata, true) : [];
        if ($request->has('header')) {
            $metadata['header'] = $request->header;
        }
        if ($request->has('body')) {
            $metadata['body'] = $request->body;
        }
        if ($request->has('footer')) {
            $metadata['footer'] = $request->footer;
        }
        if ($request->has('buttons')) {
            $metadata['buttons'] = $request->buttons;
        }

        $template->name = $request->name;
        $template->category = $request->category;
        $template->language = $request->language;
        $template->metadata = json_encode($metadata);
        $template->updated_at = now();
        $template->save();

        return response()->json([
            'success' => true,
            'message' => __('Template updated successfully'),
            'template' => $template
        ]);
    }

    public function deleteTemplate($uuid)
    {
        $query = $this->whatsappService->deleteTemplate($uuid);

        if($query->success === true){
            return response()->json([
                'success' => true,
                'message'=> __('Template deleted successfully')
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message'=> __('something went wrong. Refresh the page and try again')
            ]);
        }
    }
}