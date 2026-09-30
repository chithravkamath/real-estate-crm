<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Communication;
use App\Models\Lead;
use App\Models\Client;
use Carbon\Carbon;

class CommunicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

        // Now seed communications!
        Communication::create([
            'lead_id' => $lead->id,
            'client_id' => null,
            'communication_type' => 'Call',
            'communication_date' => Carbon::today()->subDays(3)->toDateString(),
            'notes' => 'Called John Doe to introduce Greenwood Residency. He expressed interest and requested an email brochure.',
            'created_by' => 'Admin User',
        ]);

        Communication::create([
            'lead_id' => $lead->id,
            'client_id' => null,
            'communication_type' => 'Email',
            'communication_date' => Carbon::today()->subDays(2)->toDateString(),
            'notes' => 'Emailed Greenwood Residency PDF brochure and budget estimations to John Doe.',
            'created_by' => 'Admin User',
        ]);

        Communication::create([
            'lead_id' => null,
            'client_id' => $client->id,
            'communication_type' => 'Meeting',
            'communication_date' => Carbon::today()->subDays(1)->toDateString(),
            'notes' => 'In-person meeting with Robert Smith at the office. Discussed premium modular kitchen layout and negotiated token amount.',
            'created_by' => 'Admin User',
        ]);

        Communication::create([
            'lead_id' => null,
            'client_id' => $client->id,
            'communication_type' => 'WhatsApp',
            'communication_date' => Carbon::today()->toDateString(),
            'notes' => 'Sent location pin of Oakwood Villa via WhatsApp and confirmed meeting time.',
            'created_by' => 'Admin User',
        ]);
    }
}
