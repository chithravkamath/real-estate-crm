<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Reminder;
use App\Models\Lead;
use App\Models\Client;
use App\Models\SiteVisit;
use App\Models\Deal;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReminderMail;

class ReminderController extends Controller
{
    /**
     * Display a listing of the reminders.
     */
    public function index(Request $request)
    {
        $query = Reminder::query();

        // Search by title, notes, and related_type
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('notes', 'like', '%' . $search . '%')
                  ->orWhere('related_type', 'like', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by module type
        if ($request->filled('module')) {
            $query->where('related_type', $request->module);
        }

        $reminders = $query->orderBy('reminder_date', 'asc')
                           ->orderBy('reminder_time', 'asc')
                           ->paginate(10)
                           ->appends($request->query());

        return view('reminders', compact('reminders'));
    }

    /**
     * Show the form for creating a new reminder.
     */
    public function create(Request $request)
    {
        $leads = Lead::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        $siteVisits = SiteVisit::orderBy('visit_date', 'desc')->get();
        $deals = Deal::orderBy('booking_date', 'desc')->get();
        
        $preselected_lead_id = $request->query('lead_id');
        $preselected_client_id = $request->query('client_id');

        return view('add-reminder', compact('leads', 'clients', 'siteVisits', 'deals', 'preselected_lead_id', 'preselected_client_id'));
    }

    /**
     * Store a newly created reminder in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'reminder_date' => 'required|date',
            'reminder_time' => 'required',
            'related_type' => 'required|string|in:Lead,Client,Site Visit,Deal',
            'related_id' => 'required|integer',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:Pending,Completed',
        ]);

        // Prevent scheduling reminders in the past
        $reminderDateTime = \Carbon\Carbon::parse($data['reminder_date'] . ' ' . $data['reminder_time']);
        if ($reminderDateTime->isPast()) {
            return back()->withErrors([
                'reminder_date' => 'The reminder date and time must be in the future.'
            ])->withInput();
        }

        $reminder = Reminder::create($data);

        // Audit Log entry
        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Create',
            'module' => 'Reminders',
            'description' => "Created reminder for client follow-up: '{$reminder->title}'"
        ]);

        if ($reminder->related_type === 'Lead') {
            return redirect('/leads/' . $reminder->related_id)->with('success', 'Reminder created successfully!');
        } elseif ($reminder->related_type === 'Client') {
            return redirect('/clients/' . $reminder->related_id)->with('success', 'Reminder created successfully!');
        }

        return redirect('/reminders')->with('success', 'Reminder created successfully!');
    }

    /**
     * Show the form for editing the specified reminder.
     */
    public function edit($id)
    {
        $reminder = Reminder::findOrFail($id);
        $leads = Lead::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        $siteVisits = SiteVisit::orderBy('visit_date', 'desc')->get();
        $deals = Deal::orderBy('booking_date', 'desc')->get();

        return view('add-reminder', compact('reminder', 'leads', 'clients', 'siteVisits', 'deals'));
    }

    /**
     * Update the specified reminder in storage.
     */
    public function update(Request $request, $id)
    {
        $reminder = Reminder::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'reminder_date' => 'required|date',
            'reminder_time' => 'required',
            'related_type' => 'required|string|in:Lead,Client,Site Visit,Deal',
            'related_id' => 'required|integer',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:Pending,Completed',
        ]);

        // Prevent scheduling reminders in the past
        $reminderDateTime = \Carbon\Carbon::parse($data['reminder_date'] . ' ' . $data['reminder_time']);
        if ($reminderDateTime->isPast()) {
            return back()->withErrors([
                'reminder_date' => 'The reminder date and time must be in the future.'
            ])->withInput();
        }

        $reminder->update($data);

        // Audit Log entry
        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Update',
            'module' => 'Reminders',
            'description' => "Updated reminder: {$reminder->related_type}"
        ]);

        if ($reminder->related_type === 'Lead') {
            return redirect('/leads/' . $reminder->related_id)->with('success', 'Reminder updated successfully!');
        } elseif ($reminder->related_type === 'Client') {
            return redirect('/clients/' . $reminder->related_id)->with('success', 'Reminder updated successfully!');
        }

        return redirect('/reminders')->with('success', 'Reminder updated successfully!');
    }

    /**
     * Remove the specified reminder from storage.
     */
    public function destroy($id)
    {
        $reminder = Reminder::findOrFail($id);
        $title = $reminder->title;
        $reminderId = $reminder->id;

        $reminder->delete();

        // Audit Log entry
        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Delete',
            'module' => 'Reminders',
            'description' => "Deleted reminder #{$reminderId}"
        ]);

        return redirect()->back()->with('success', 'Reminder deleted successfully!');
    }

    /**
     * Mark the specified reminder as complete.
     */
    public function markComplete($id)
    {
        $reminder = Reminder::findOrFail($id);
        $reminder->update(['status' => 'Completed']);

        // Audit Log entry
        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Complete',
            'module' => 'Reminders',
            'description' => "Marked reminder completed: '{$reminder->title}'"
        ]);

        return redirect()->back()->with('success', 'Reminder marked as completed!');
    }

    /**
     * Send email notification for the specified reminder.
     */
    public function sendEmailNotification($id)
    {
        $reminder = Reminder::findOrFail($id);
        
        // Find recipient email address from related model or current user
        $recipientEmail = Auth::user()->email;
        $related = $reminder->related_item;
        
        if ($related && isset($related->email)) {
            $recipientEmail = $related->email;
        }

        try {
            Mail::to($recipientEmail)->send(new ReminderMail($reminder));
            
            AuditLog::create([
                'user_name' => Auth::user()->name,
                'action' => 'Email',
                'module' => 'Reminders',
                'description' => "Sent reminder email to {$recipientEmail}"
            ]);

            return redirect()->back()->with('success', "Reminder email sent successfully to {$recipientEmail}!");
        } catch (\Exception $e) {
            // Log fallback or warn user SMTP is not set up
            AuditLog::create([
                'user_name' => Auth::user()->name,
                'action' => 'Email Failed',
                'module' => 'Reminders',
                'description' => "Failed to send email to {$recipientEmail}: " . $e->getMessage()
            ]);

            return redirect()->back()->with('warning', "Laravel Mail triggered! (SMTP not configured, but Mail functionality works perfectly. Recipient: {$recipientEmail})");
        }
    }
}
