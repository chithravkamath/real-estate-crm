<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('property_interest')) {
            $query->where('property_interest', $request->property_interest);
        }

        if ($request->filled('budget_range')) {
            if ($request->budget_range == 'under_150k') {
                $query->whereRaw('CAST(budget AS UNSIGNED) < 150000');
            } elseif ($request->budget_range == '150k_500k') {
                $query->whereRaw('CAST(budget AS UNSIGNED) >= 150000 AND CAST(budget AS UNSIGNED) <= 500000');
            } elseif ($request->budget_range == '500k_1m') {
                $query->whereRaw('CAST(budget AS UNSIGNED) >= 500000 AND CAST(budget AS UNSIGNED) <= 1000000');
            } elseif ($request->budget_range == '1m_plus') {
                $query->whereRaw('CAST(budget AS UNSIGNED) > 1000000');
            }
        }

        $clients = $query->orderBy('name')->paginate(10);

        $properties = \App\Models\Property::pluck('property_name')
            ->merge(Client::whereNotNull('property_interest')->distinct()->pluck('property_interest'))
            ->unique()
            ->filter()
            ->values();

        return view('clients', compact('clients', 'properties'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:2|max:255|regex:/^[a-zA-Z\s\.\'\-]+$/',
            'email' => 'required|email|unique:clients,email|unique:users,email',
            'phone' => 'required|digits:10',
            'status' => 'nullable|string|max:100',
            'property' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if (User::where('email', $data['email'])->exists()) {
            return back()->withErrors([
                'email' => 'A user with this email already exists.'
            ]);
        }

        // STEP 1: Create user account first
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make('client123'),
            'role' => 'client',
        ]);

        // STEP 2: Create linked client CRM record
        Client::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'] ?? null,
            'property_interest' => $data['property'] ?? null,
            'budget' => $data['budget'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Add',
            'module' => 'Client',
            'description' => 'Added new client: ' . $data['name']
        ]);

        return redirect('/clients')
            ->with('success', 'Client added successfully. Default password: client123');
    }
    public function edit($id)
    {
        $client = Client::findOrFail($id);

        return view('add-client', compact('client'));
    }

    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|min:2|max:255|regex:/^[a-zA-Z\s\.\'\-]+$/',
            'email' => 'required|email|unique:clients,email,' . $client->id . '|unique:users,email,' . ($client->user_id ?? 0),
            'phone' => 'required|digits:10',
            'status' => 'nullable|string|max:100',
            'property' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $client->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'] ?? null,
            'property_interest' => $data['property'] ?? null,
            'budget' => $data['budget'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        // STEP 3: Sync linked user record if it exists
        if ($client->user_id && $user = User::find($client->user_id)) {
            $user->update([
                'name' => $data['name'],
                'email' => $data['email']
            ]);
        }

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Edit',
            'module' => 'Client',
            'description' => 'Edited client: ' . $client->name
        ]);

        return redirect('/clients')
            ->with('success', 'Client updated successfully.');
    }

    public function show($id)
    {
        $client = Client::findOrFail($id);
        
        $communications = $client->communications()
            ->orderBy('communication_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $reminders = $client->reminders()
            ->orderBy('reminder_date', 'desc')
            ->orderBy('reminder_time', 'desc')
            ->get();
            
        $siteVisits = \App\Models\SiteVisit::where('client_id', $client->id)
            ->orWhere(function ($query) use ($client) {
                $query->whereNull('client_id')->where('client_name', $client->name);
            })
            ->orderBy('visit_date', 'desc')
            ->orderBy('visit_time', 'desc')
            ->get();

        return view('client-details', compact('client', 'communications', 'reminders', 'siteVisits'));
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);

        \Illuminate\Support\Facades\DB::transaction(function () use ($client) {
            // 1. reminders
            \App\Models\Reminder::where('related_type', 'Client')
                ->where('related_id', $client->id)
                ->delete();

            // 2. communications
            \App\Models\Communication::where('client_id', $client->id)->delete();

            // 3. site visits
            if (\Illuminate\Support\Facades\Schema::hasColumn('site_visits', 'client_id')) {
                \App\Models\SiteVisit::where('client_id', $client->id)
                    ->orWhere(function ($query) use ($client) {
                        $query->whereNull('client_id')->where('client_name', $client->name);
                    })->delete();
            } else {
                \App\Models\SiteVisit::where('client_name', $client->name)->delete();
            }

            // 4. billings/payments
            if (\Illuminate\Support\Facades\Schema::hasColumn('billings', 'client_id')) {
                \App\Models\Billing::where('client_id', $client->id)
                    ->orWhere(function ($query) use ($client) {
                        $query->whereNull('client_id')->where('client_name', $client->name);
                    })->delete();
            } else {
                \App\Models\Billing::where('client_name', $client->name)->delete();
            }

            // 5. deals
            if (\Illuminate\Support\Facades\Schema::hasColumn('deals', 'client_id')) {
                \App\Models\Deal::where('client_id', $client->id)
                    ->orWhere(function ($query) use ($client) {
                        $query->whereNull('client_id')->where('client_name', $client->name);
                    })->delete();
            } else {
                \App\Models\Deal::where('client_name', $client->name)->delete();
            }

            // Optional: interested properties (Leads)
            \App\Models\Lead::where('email', $client->email)->delete();

            // 6. client record
            $client->delete();

            // 7. linked user account
            User::where('email', $client->email)->delete();
        });

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Delete',
            'module' => 'Client',
            'description' => 'Deleted client and related CRM records: ' . $client->name
        ]);

        return redirect('/clients')
            ->with('success', 'Client and related CRM records deleted successfully.');
    }
}
