<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reminder;
use App\Models\Lead;
use App\Models\Client;
use App\Models\SiteVisit;
use App\Models\Deal;
use Carbon\Carbon;

class ReminderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure we have a Lead
        $lead = Lead::first();
        if (!$lead) {
            $lead = Lead::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '1234567890',
                'source' => 'Website',
                'status' => 'New',
                'interested_property' => 'Greenwood Residency',
                'budget' => '$500,000',
                'notes' => 'Looking for a 3BHK flat.',
                'agent' => 'Admin User',
            ]);
        }

        // 2. Ensure we have a Client
        $client = Client::first();
        if (!$client) {
            $client = Client::create([
                'name' => 'Robert Smith',
                'email' => 'robert@example.com',
                'phone' => '0987654321',
                'status' => 'Active',
                'property_interest' => 'Oakwood Villa',
                'budget' => '$1,200,000',
                'notes' => 'Needs premium modular kitchen.',
            ]);
        }

        // 3. Ensure we have a Site Visit
        $visit = SiteVisit::first();
        if (!$visit) {
            $visit = SiteVisit::create([
                'client_name' => 'Robert Smith',
                'property_name' => 'Oakwood Villa',
                'visit_date' => Carbon::tomorrow(),
                'visit_time' => '14:30:00',
                'agent_name' => 'Admin User',
                'status' => 'Scheduled',
                'notes' => 'Client wants a complete walkthrough.',
            ]);
        }

        // 4. Ensure we have a Deal
        $deal = Deal::first();
        if (!$deal) {
            $deal = Deal::create([
                'client_name' => 'Robert Smith',
                'property_name' => 'Oakwood Villa',
                'agent_name' => 'Admin User',
                'deal_amount' => '1150000',
                'commission_percentage' => '5',
                'commission_amount' => '57500',
                'booking_date' => Carbon::yesterday(),
                'status' => 'In Progress',
                'payment_status' => 'Partial',
                'notes' => 'Booking token received.',
            ]);
        }

        // Now create reminders!
        // Reminder 1: Today's pending reminder (Lead follow up)
        Reminder::create([
            'title' => 'Follow up with John Doe regarding budget fit',
            'reminder_date' => Carbon::today()->toDateString(),
            'reminder_time' => '10:00:00',
            'related_type' => 'Lead',
            'related_id' => $lead->id,
            'notes' => 'Call him to check if Greenwood Residency fits his extended budget.',
            'status' => 'Pending',
        ]);

        // Reminder 2: Today's pending reminder (Deal signing check)
        Reminder::create([
            'title' => 'Review final agreement paperwork',
            'reminder_date' => Carbon::today()->toDateString(),
            'reminder_time' => '15:30:00',
            'related_type' => 'Deal',
            'related_id' => $deal->id,
            'notes' => 'Ensure commission percentages and terms are fully aligned.',
            'status' => 'Pending',
        ]);

        // Reminder 3: Upcoming pending reminder (Site visit prep)
        Reminder::create([
            'title' => 'Call Robert to confirm Oakwood Villa visit',
            'reminder_date' => Carbon::tomorrow()->toDateString(),
            'reminder_time' => '11:00:00',
            'related_type' => 'Site Visit',
            'related_id' => $visit->id,
            'notes' => 'Confirm pick up point and agent availability.',
            'status' => 'Pending',
        ]);

        // Reminder 4: Upcoming pending reminder (Client follow up)
        Reminder::create([
            'title' => 'Send list of alternative villas',
            'reminder_date' => Carbon::today()->addDays(3)->toDateString(),
            'reminder_time' => '09:00:00',
            'related_type' => 'Client',
            'related_id' => $client->id,
            'notes' => 'Email backup choices if Oakwood Villa negotiations stall.',
            'status' => 'Pending',
        ]);

        // Reminder 5: Completed reminder (Initial contact)
        Reminder::create([
            'title' => 'Initial inquiry response',
            'reminder_date' => Carbon::today()->subDays(2)->toDateString(),
            'reminder_time' => '16:00:00',
            'related_type' => 'Lead',
            'related_id' => $lead->id,
            'notes' => 'Sent welcome details and brochure pdf.',
            'status' => 'Completed',
        ]);
    }
}
