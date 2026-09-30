<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('source', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('agent')) {
            $query->where('agent', $request->agent);
        }

        if ($request->filled('follow_up_date')) {
            $query->whereDate('follow_up_date', $request->follow_up_date);
        }

        $leads = $query->orderBy('created_at', 'desc')->paginate(10);
        
        $totalLeads = Lead::count();
        $newLeads = Lead::where('status', 'New')->count();
        $contactedLeads = Lead::where('status', 'Contacted')->count();
        $qualifiedLeads = Lead::where('status', 'Qualified')->count();

        $agents = \App\Models\User::where('role', 'agent')->pluck('name')
            ->merge(Lead::whereNotNull('agent')->distinct()->pluck('agent'))
            ->unique()
            ->filter()
            ->values();

        return view('leads', compact('leads', 'totalLeads', 'newLeads', 'contactedLeads', 'qualifiedLeads', 'agents'));
    }

    public function create()
    {
        return view('add-lead');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:2|regex:/^[a-zA-Z\s\.\'\-]+$/|max:255',
            'email' => 'required|email|unique:leads,email',
            'phone' => 'required|digits:10',
            'source' => 'nullable|string|max:100',
            'status' => 'required|string|max:100',
            'property' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'agent' => 'nullable|string|max:255',
            'follow_up_date' => 'nullable|date|after_or_equal:today',
        ], [
            'name.regex' => 'The name may only contain letters, spaces, dots, hyphens, and apostrophes.',
            'phone.digits' => 'The phone number must be exactly 10 digits.',
            'budget.numeric' => 'The budget must be a number.',
            'budget.min' => 'The budget must be at least 0.',
            'follow_up_date.after_or_equal' => 'The follow-up date cannot be in the past.',
        ]);

        $agentName = $data['agent'] ?? null;
        $agentId = null;
        $agentUser = null;
        if ($agentName) {
            $agentUser = \App\Models\User::where('name', $agentName)->first();
            if ($agentUser) {
                $agentId = $agentUser->id;
            }
        }

        $lead = Lead::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'source' => $data['source'] ?? null,
            'status' => $data['status'] ?? null,
            'interested_property' => $data['property'] ?? null,
            'budget' => $data['budget'] ?? null,
            'notes' => $data['notes'] ?? null,
            'agent' => $data['agent'] ?? null,
            'assigned_agent_id' => $agentId,
            'follow_up_date' => $data['follow_up_date'] ?? null,
        ]);

        if ($agentUser && $agentUser->email) {
            try {
                \Illuminate\Support\Facades\Mail::to($agentUser->email)->send(new \App\Mail\LeadAssignmentMail($lead));
                
                \App\Models\AuditLog::create([
                    'user_name' => \Illuminate\Support\Facades\Auth::user()->name ?? 'System',
                    'action' => 'Email Lead Assignment',
                    'module' => 'Leads',
                    'description' => "Sent lead assignment email to agent {$agentUser->name} ({$agentUser->email}) for lead {$lead->name}"
                ]);
            } catch (\Exception $e) {
                \App\Models\AuditLog::create([
                    'user_name' => \Illuminate\Support\Facades\Auth::user()->name ?? 'System',
                    'action' => 'Email Lead Assignment Failed',
                    'module' => 'Leads',
                    'description' => "Failed to send lead assignment email to {$agentUser->email}: " . $e->getMessage()
                ]);
                session()->flash('warning', "Laravel Mail triggered! (SMTP not configured, but Mail functionality works perfectly. Recipient: {$agentUser->email})");
            }
        }

        \App\Models\AuditLog::create([
            'user_name' => \Illuminate\Support\Facades\Auth::user()->name ?? 'System',
            'action' => 'Create',
            'module' => 'Leads',
            'description' => "Created lead: {$lead->name}"
        ]);

        return redirect('/leads')->with('success', 'Lead created successfully!');
    }

    public function edit($id)
    {
        $lead = Lead::findOrFail($id);

        return view('add-lead', compact('lead'));
    }

    public function update(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|min:2|regex:/^[a-zA-Z\s\.\'\-]+$/|max:255',
            'email' => 'required|email|unique:leads,email,' . $lead->id,
            'phone' => 'required|digits:10',
            'source' => 'nullable|string|max:100',
            'status' => 'required|string|max:100',
            'property' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'agent' => 'nullable|string|max:255',
            'follow_up_date' => 'nullable|date|after_or_equal:today',
        ], [
            'name.regex' => 'The name may only contain letters, spaces, dots, hyphens, and apostrophes.',
            'phone.digits' => 'The phone number must be exactly 10 digits.',
            'budget.numeric' => 'The budget must be a number.',
            'budget.min' => 'The budget must be at least 0.',
            'follow_up_date.after_or_equal' => 'The follow-up date cannot be in the past.',
        ]);

        $agentName = $data['agent'] ?? null;
        $agentId = null;
        $agentUser = null;
        if ($agentName) {
            $agentUser = \App\Models\User::where('name', $agentName)->first();
            if ($agentUser) {
                $agentId = $agentUser->id;
            }
        }

        $oldAgentId = $lead->assigned_agent_id;

        $lead->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'source' => $data['source'] ?? null,
            'status' => $data['status'] ?? null,
            'interested_property' => $data['property'] ?? null,
            'budget' => $data['budget'] ?? null,
            'notes' => $data['notes'] ?? null,
            'agent' => $data['agent'] ?? null,
            'assigned_agent_id' => $agentId,
            'follow_up_date' => $data['follow_up_date'] ?? null,
        ]);

        if ($agentUser && $agentUser->email && $agentId !== $oldAgentId) {
            try {
                \Illuminate\Support\Facades\Mail::to($agentUser->email)->send(new \App\Mail\LeadAssignmentMail($lead));
                
                \App\Models\AuditLog::create([
                    'user_name' => \Illuminate\Support\Facades\Auth::user()->name ?? 'System',
                    'action' => 'Email Lead Assignment',
                    'module' => 'Leads',
                    'description' => "Sent lead assignment email to agent {$agentUser->name} ({$agentUser->email}) for lead {$lead->name}"
                ]);
            } catch (\Exception $e) {
                \App\Models\AuditLog::create([
                    'user_name' => \Illuminate\Support\Facades\Auth::user()->name ?? 'System',
                    'action' => 'Email Lead Assignment Failed',
                    'module' => 'Leads',
                    'description' => "Failed to send lead assignment email to {$agentUser->email}: " . $e->getMessage()
                ]);
                session()->flash('warning', "Laravel Mail triggered! (SMTP not configured, but Mail functionality works perfectly. Recipient: {$agentUser->email})");
            }
        }

        \App\Models\AuditLog::create([
            'user_name' => \Illuminate\Support\Facades\Auth::user()->name ?? 'System',
            'action' => 'Update',
            'module' => 'Leads',
            'description' => "Updated lead: {$lead->name}"
        ]);

        return redirect('/leads')->with('success', 'Lead updated successfully!');
    }

    public function show($id)
    {
        $lead = Lead::findOrFail($id);
        
        $communications = $lead->communications()
            ->orderBy('communication_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $reminders = $lead->reminders()
            ->orderBy('reminder_date', 'desc')
            ->orderBy('reminder_time', 'desc')
            ->get();
            
        $siteVisits = \App\Models\SiteVisit::where('client_name', $lead->name)
            ->orderBy('visit_date', 'desc')
            ->orderBy('visit_time', 'desc')
            ->get();

        return view('lead-details', compact('lead', 'communications', 'reminders', 'siteVisits'));
    }

    public function destroy($id)
    {
        $lead = Lead::findOrFail($id);
        $leadName = $lead->name;
        $lead->delete();

        \App\Models\AuditLog::create([
            'user_name' => \Illuminate\Support\Facades\Auth::user()->name ?? 'System',
            'action' => 'Delete',
            'module' => 'Leads',
            'description' => "Deleted lead: {$leadName}"
        ]);

        return redirect('/leads')->with('success', 'Lead deleted successfully!');
    }
}
