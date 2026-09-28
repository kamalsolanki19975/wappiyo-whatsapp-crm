<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as BaseController;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LeadController extends BaseController
{
    /**
     * Display a listing of website leads.
     */
    public function index(Request $request)
    {
        $query = Lead::with(['assignedAdmin', 'convertedOrganization'])->latest();

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // Form type filter
        if ($formType = $request->input('form_type')) {
            if ($formType !== 'all') {
                $query->where('form_type', $formType);
            }
        }

        // Source filter
        if ($source = $request->input('source')) {
            if ($source !== 'all') {
                $query->where('source', $source);
            }
        }

        // Assignment filter
        if ($assignedTo = $request->input('assigned_to')) {
            if ($assignedTo === 'unassigned') {
                $query->whereNull('assigned_to');
            } elseif ($assignedTo !== 'all') {
                $query->where('assigned_to', $assignedTo);
            }
        }

        $leads = $query->paginate(15)->withQueryString();

        // Real calculated metrics from database
        $metrics = [
            'total' => Lead::count(),
            'new' => Lead::where('status', 'new')->count(),
            'contacted' => Lead::where('status', 'contacted')->count(),
            'qualified' => Lead::where('status', 'qualified')->count(),
            'converted' => Lead::where('status', 'converted')->count(),
            'lost' => Lead::where('status', 'lost')->count(),
            'today' => Lead::whereBetween('created_at', [
                today()->startOfDay(),
                today()->endOfDay(),
            ])->count(),
        ];

        // Admin staff for assignment dropdown
        $adminUsers = User::where('role', 'admin')
            ->select('id', 'first_name', 'last_name', 'email')
            ->get();

        return Inertia::render('Admin/Lead/Index', [
            'title' => __('Website Leads'),
            'leads' => $leads,
            'filters' => $request->only(['search', 'status', 'form_type', 'source', 'assigned_to']),
            'metrics' => $metrics,
            'adminUsers' => $adminUsers,
        ]);
    }

    /**
     * Display a specific lead's complete details.
     */
    public function show($uuid)
    {
        $lead = Lead::where('uuid', $uuid)
            ->with(['assignedAdmin', 'convertedOrganization'])
            ->firstOrFail();

        $adminUsers = User::where('role', 'admin')
            ->select('id', 'first_name', 'last_name', 'email')
            ->get();

        $organizations = Organization::whereNull('deleted_at')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Lead/Show', [
            'title' => __('Lead Details: ') . $lead->name,
            'lead' => $lead,
            'adminUsers' => $adminUsers,
            'organizations' => $organizations,
        ]);
    }

    /**
     * Update lead status.
     */
    public function updateStatus(Request $request, $uuid)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,qualified,converted,lost',
        ]);

        $lead = Lead::where('uuid', $uuid)->firstOrFail();
        $oldStatus = $lead->status;
        $lead->status = $validated['status'];

        if ($validated['status'] === 'converted' && !$lead->converted_at) {
            $lead->converted_at = now();
        }

        $adminUser = Auth::guard('admin')->user() ?? Auth::user();
        $adminName = $adminUser ? ($adminUser->first_name . ' ' . $adminUser->last_name) : 'Admin';

        $lead->addNote('System', "Status updated from '{$oldStatus}' to '{$validated['status']}' by {$adminName}");

        return redirect()->back()->with('status', [
            'type' => 'success',
            'message' => __('Lead status updated successfully.')
        ]);
    }

    /**
     * Assign lead to an admin user.
     */
    public function assign(Request $request, $uuid)
    {
        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $lead = Lead::where('uuid', $uuid)->firstOrFail();
        $lead->assigned_to = $validated['assigned_to'] ?? null;
        $lead->save();

        $assignedUser = $lead->assigned_to ? User::find($lead->assigned_to) : null;
        $assignedName = $assignedUser ? ($assignedUser->first_name . ' ' . $assignedUser->last_name) : 'Unassigned';

        $adminUser = Auth::guard('admin')->user() ?? Auth::user();
        $adminName = $adminUser ? ($adminUser->first_name . ' ' . $adminUser->last_name) : 'Admin';

        $lead->addNote('System', "Assigned to {$assignedName} by {$adminName}");

        return redirect()->back()->with('status', [
            'type' => 'success',
            'message' => __('Lead assignment updated.')
        ]);
    }

    /**
     * Add an internal note to the lead timeline.
     */
    public function addNote(Request $request, $uuid)
    {
        $validated = $request->validate([
            'note' => 'required|string|max:2000',
        ]);

        $lead = Lead::where('uuid', $uuid)->firstOrFail();

        $adminUser = Auth::guard('admin')->user() ?? Auth::user();
        $author = $adminUser ? trim($adminUser->first_name . ' ' . $adminUser->last_name) : 'Admin';

        $lead->addNote($author ?: 'Admin', $validated['note']);

        return redirect()->back()->with('status', [
            'type' => 'success',
            'message' => __('Note added successfully.')
        ]);
    }

    /**
     * Convert lead to Contact inside an Organization.
     */
    public function convert(Request $request, $uuid)
    {
        $lead = Lead::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
        ]);

        $organization = Organization::findOrFail($validated['organization_id']);

        // Check if Contact with identical email/phone already exists in this organization (PART 16)
        $existingContact = Contact::where('organization_id', $organization->id)
            ->where(function ($q) use ($lead) {
                if ($lead->email) {
                    $q->where('email', $lead->email);
                }
                if ($lead->phone) {
                    $q->orWhere('phone', $lead->phone);
                }
            })
            ->whereNull('deleted_at')
            ->first();

        if (!$existingContact) {
            $nameParts = explode(' ', trim($lead->name), 2);
            Contact::create([
                'organization_id' => $organization->id,
                'first_name' => $lead->first_name ?: ($nameParts[0] ?? $lead->name),
                'last_name' => $lead->last_name ?: ($nameParts[1] ?? ''),
                'email' => $lead->email,
                'phone' => $lead->phone ?: null,
                'created_by' => $organization->created_by ?: 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $lead->status = 'converted';
        $lead->converted_to_organization_id = $organization->id;
        $lead->converted_at = now();

        $adminUser = Auth::guard('admin')->user() ?? Auth::user();
        $adminName = $adminUser ? ($adminUser->first_name . ' ' . $adminUser->last_name) : 'Admin';

        $lead->addNote('System', "Converted to Contact in organization '{$organization->name}' by {$adminName}");

        return redirect()->back()->with('status', [
            'type' => 'success',
            'message' => __('Lead converted to CRM contact successfully!')
        ]);
    }

    /**
     * Delete lead.
     */
    public function destroy($uuid)
    {
        $lead = Lead::where('uuid', $uuid)->firstOrFail();
        $lead->delete();

        return redirect()->route('admin.leads.index')->with('status', [
            'type' => 'success',
            'message' => __('Lead deleted successfully.')
        ]);
    }
}
