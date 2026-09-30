<?php

namespace App\Http\Controllers;

use App\Models\SiteVisit;
use App\Models\Property;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class SiteVisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SiteVisit::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', '%' . $search . '%')
                  ->orWhere('property_name', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $visits = $query->orderBy('visit_date', 'desc')
            ->orderBy('visit_time')
            ->paginate(10);

        return view('site-visits', compact('visits'));
    }

    /**
     * Show the form for creating a new resource.
     */
   public function create()
{
    $properties = Property::all();

    $clients = Client::all();

    $agents = User::where('role', 'agent')->get();

    return view('add-visit', compact(
        'properties',
        'clients',
        'agents'
    ));
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'property' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'agent' => 'required|string|max:255',
            'status' => 'required|string|max:100',
            'notes' => 'nullable|string',
        ], [
            'date.after_or_equal' => 'The site visit date cannot be in the past.',
        ]);

        $clientModel = Client::findOrFail($data['client_id']);

        $agentName = $data['agent'] ?? null;
        $agentId = null;
        if ($agentName) {
            $agentUser = User::where('name', $agentName)->first();
            if ($agentUser) {
                $agentId = $agentUser->id;
            }
        }

        $visit = SiteVisit::create([
            'client_id' => $clientModel->id,
            'client_name' => $clientModel->name,
            'property_name' => $data['property'],
            'visit_date' => $data['date'],
            'visit_time' => $data['time'],
            'agent_name' => $data['agent'],
            'agent_id' => $agentId,
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
        ]);

        \App\Models\AuditLog::create([
            'user_name' => \Illuminate\Support\Facades\Auth::user()->name ?? 'System',
            'action' => 'Create',
            'module' => 'Site Visits',
            'description' => "Scheduled site visit #{$visit->id} for client '{$clientModel->name}' on property '{$visit->property_name}'"
        ]);

        return redirect('/site-visits')->with('success', 'Site visit scheduled successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(SiteVisit $siteVisit)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SiteVisit $siteVisit)
{
    $properties = Property::all();

    $clients = Client::all();

    $agents = User::where('role', 'agent')->get();

    return view('add-visit', compact(
        'siteVisit',
        'properties',
        'clients',
        'agents'
    ))->with('visit', $siteVisit);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SiteVisit $siteVisit)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'property' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'agent' => 'required|string|max:255',
            'status' => 'required|string|max:100',
            'notes' => 'nullable|string',
        ], [
            'date.after_or_equal' => 'The site visit date cannot be in the past.',
        ]);

        $clientModel = Client::findOrFail($data['client_id']);

        $agentName = $data['agent'] ?? null;
        $agentId = null;
        if ($agentName) {
            $agentUser = User::where('name', $agentName)->first();
            if ($agentUser) {
                $agentId = $agentUser->id;
            }
        }

        $siteVisit->update([
            'client_id' => $clientModel->id,
            'client_name' => $clientModel->name,
            'property_name' => $data['property'],
            'visit_date' => $data['date'],
            'visit_time' => $data['time'],
            'agent_name' => $data['agent'],
            'agent_id' => $agentId,
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
        ]);

        \App\Models\AuditLog::create([
            'user_name' => \Illuminate\Support\Facades\Auth::user()->name ?? 'System',
            'action' => 'Update',
            'module' => 'Site Visits',
            'description' => "Updated site visit #{$siteVisit->id} for client '{$clientModel->name}'"
        ]);

        return redirect('/site-visits')->with('success', 'Site visit updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SiteVisit $siteVisit)
    {
        $visitId = $siteVisit->id;
        $clientName = $siteVisit->client_name;
        $siteVisit->delete();

        \App\Models\AuditLog::create([
            'user_name' => \Illuminate\Support\Facades\Auth::user()->name ?? 'System',
            'action' => 'Delete',
            'module' => 'Site Visits',
            'description' => "Deleted site visit #{$visitId} for client '{$clientName}'"
        ]);

        return redirect('/site-visits')->with('success', 'Site visit deleted successfully!');
    }
}
