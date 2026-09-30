<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Communication;
use App\Models\Lead;
use App\Models\Client;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class CommunicationController extends Controller
{
    /**
     * Display a listing of the communications with search and filters.
     */
    public function index(Request $request)
    {
        $query = Communication::with(['lead', 'client']);

        // Search by notes, type, and associated lead/client names
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', '%' . $search . '%')
                  ->orWhere('communication_type', 'like', '%' . $search . '%')
                  ->orWhereHas('lead', function ($ql) use ($search) {
                      $ql->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('client', function ($qc) use ($search) {
                      $qc->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by communication type
        if ($request->filled('type')) {
            $query->where('communication_type', $request->type);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('communication_date', $request->date);
        }

        $communications = $query->orderBy('communication_date', 'desc')
                                ->orderBy('created_at', 'desc')
                                ->paginate(10)
                                ->appends($request->query());

        return view('communications', compact('communications'));
    }

    /**
     * Show the form for creating a new communication log.
     */
    public function create(Request $request)
    {
        $leads = Lead::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        
        $preselected_lead_id = $request->query('lead_id');
        $preselected_client_id = $request->query('client_id');

        return view('add-communication', compact('leads', 'clients', 'preselected_lead_id', 'preselected_client_id'));
    }

    /**
     * Store a newly created communication log in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'contact_type' => 'required|string|in:Lead,Client',
            'lead_id' => 'required_if:contact_type,Lead|nullable|integer|exists:leads,id',
            'client_id' => 'required_if:contact_type,Client|nullable|integer|exists:clients,id',
            'communication_type' => 'required|string|in:Call,Email,Meeting,WhatsApp',
            'communication_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string',
        ], [
            'lead_id.required_if' => 'Please select a lead.',
            'client_id.required_if' => 'Please select a client.',
            'communication_date.before_or_equal' => 'The communication date cannot be in the future.',
        ]);

        $communication = Communication::create([
            'lead_id' => $request->contact_type === 'Lead' ? $request->lead_id : null,
            'client_id' => $request->contact_type === 'Client' ? $request->client_id : null,
            'communication_type' => $data['communication_type'],
            'communication_date' => $data['communication_date'],
            'notes' => $data['notes'],
            'created_by' => Auth::user()->name,
        ]);

        // Audit Log entry
        $targetName = $communication->related_name;
        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Create',
            'module' => 'Communications',
            'description' => "Logged '{$communication->communication_type}' communication with '{$targetName}'"
        ]);

        // If preselected redirect back to details pages
        if ($request->contact_type === 'Lead' && $request->lead_id) {
            return redirect('/leads/' . $request->lead_id)->with('success', 'Communication logged successfully!');
        } elseif ($request->contact_type === 'Client' && $request->client_id) {
            return redirect('/clients/' . $request->client_id)->with('success', 'Communication logged successfully!');
        }

        return redirect('/communications')->with('success', 'Communication log created successfully!');
    }

    /**
     * Show the form for editing the specified communication log.
     */
    public function edit($id)
    {
        $communication = Communication::findOrFail($id);
        $leads = Lead::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();

        return view('edit-communication', compact('communication', 'leads', 'clients'));
    }

    /**
     * Update the specified communication log in storage.
     */
    public function update(Request $request, $id)
    {
        $communication = Communication::findOrFail($id);

        $data = $request->validate([
            'contact_type' => 'required|string|in:Lead,Client',
            'lead_id' => 'required_if:contact_type,Lead|nullable|integer|exists:leads,id',
            'client_id' => 'required_if:contact_type,Client|nullable|integer|exists:clients,id',
            'communication_type' => 'required|string|in:Call,Email,Meeting,WhatsApp',
            'communication_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string',
        ], [
            'lead_id.required_if' => 'Please select a lead.',
            'client_id.required_if' => 'Please select a client.',
            'communication_date.before_or_equal' => 'The communication date cannot be in the future.',
        ]);

        $communication->update([
            'lead_id' => $request->contact_type === 'Lead' ? $request->lead_id : null,
            'client_id' => $request->contact_type === 'Client' ? $request->client_id : null,
            'communication_type' => $data['communication_type'],
            'communication_date' => $data['communication_date'],
            'notes' => $data['notes'],
        ]);

        // Audit Log entry
        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Update',
            'module' => 'Communications',
            'description' => "Updated communication log #{$communication->id} for '{$communication->related_name}'"
        ]);

        return redirect('/communications')->with('success', 'Communication log updated successfully!');
    }

    /**
     * Remove the specified communication log from storage.
     */
    public function destroy($id)
    {
        $communication = Communication::findOrFail($id);
        $logId = $communication->id;
        $targetName = $communication->related_name;

        $communication->delete();

        // Audit Log entry
        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Delete',
            'module' => 'Communications',
            'description' => "Deleted communication log #{$logId} for '{$targetName}'"
        ]);

        return redirect()->back()->with('success', 'Communication log deleted successfully!');
    }
}
