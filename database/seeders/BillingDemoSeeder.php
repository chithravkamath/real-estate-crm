<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Billing;
use App\Models\Deal;
use App\Models\Client;
use App\Models\Property;
use App\Models\Expense;

class BillingDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deals = Deal::all();
        
        if ($deals->count() > 0) {
            foreach ($deals as $deal) {
                // Prevent duplicate invoice insertion if one already exists
                if (Billing::where('property_name', $deal->property_name)
                    ->where('client_name', $deal->client_name)
                    ->exists()) {
                    continue;
                }

                $paymentStatus = $deal->payment_status ?? 'Pending';
                
                $paymentAmount = floatval($deal->deal_amount);
                $advance = 0;
                $final = 0;
                $due = $paymentAmount;

                // Dynamically reconstruct advance/final payment splits based on Deal Payment Status
                if ($paymentStatus === 'Paid') {
                    $advance = $paymentAmount * 0.4;
                    $final = $paymentAmount * 0.6;
                    $due = 0;
                } elseif ($paymentStatus === 'Partial') {
                    $advance = $paymentAmount * 0.3;
                    $final = 0;
                    $due = $paymentAmount * 0.7;
                }

                Billing::create([
                    'invoice_number' => 'INV-' . str_pad($deal->id + 100, 5, '0', STR_PAD_LEFT),
                    'client_name' => $deal->client_name,
                    'client_id' => $deal->client_id,
                    'property_name' => $deal->property_name,
                    'agent_name' => $deal->agent_name,
                    'payment_amount' => $paymentAmount,
                    'advance_amount' => $advance > 0 ? $advance : null,
                    'final_amount' => $final > 0 ? $final : null,
                    'due_amount' => $due,
                    'commission' => $deal->commission_amount ?? ($paymentAmount * 0.05),
                    'payment_status' => $paymentStatus,
                    'payment_date' => $deal->booking_date ?? now()->subDays(15),
                    'due_date' => ($deal->booking_date ?? now())->addDays(30),
                    'notes' => 'Demo invoice created automatically for Deal #' . $deal->id,
                ]);
            }
        } else {
            // Fallback safety if no deals are present - query existing clients & properties
            $clients = Client::all();
            $properties = Property::all();
            
            if ($clients->count() > 0 && $properties->count() > 0) {
                $i = 1;
                foreach ($properties as $property) {
                    $client = $clients->random();
                    $price = floatval($property->price ?? 7500000);
                    
                    $paymentStatus = $i % 3 === 0 ? 'Paid' : ($i % 3 === 1 ? 'Partial' : 'Pending');
                    $advance = 0;
                    $final = 0;
                    $due = $price;

                    if ($paymentStatus === 'Paid') {
                        $advance = $price * 0.4;
                        $final = $price * 0.6;
                        $due = 0;
                    } elseif ($paymentStatus === 'Partial') {
                        $advance = $price * 0.3;
                        $final = 0;
                        $due = $price * 0.7;
                    }

                    Billing::create([
                        'invoice_number' => 'INV-' . str_pad($i + 100, 5, '0', STR_PAD_LEFT),
                        'client_name' => $client->name,
                        'client_id' => $client->id,
                        'property_name' => $property->title,
                        'agent_name' => 'Agent Cooper',
                        'payment_amount' => $price,
                        'advance_amount' => $advance > 0 ? $advance : null,
                        'final_amount' => $final > 0 ? $final : null,
                        'due_amount' => $due,
                        'commission' => $price * 0.05,
                        'payment_status' => $paymentStatus,
                        'payment_date' => now()->subDays(15),
                        'due_date' => now()->addDays(15),
                        'notes' => 'Fallback demo invoice created using client and property data',
                    ]);
                    $i++;
                }
            }
        }

        // Recreate default demo expenses if none are present
        if (Expense::count() === 0) {
            $categories = ['Marketing', 'Travel', 'Maintenance', 'Office', 'Miscellaneous'];
            $titles = [
                'Marketing' => 'Facebook Lead Ads Campaign',
                'Travel' => 'Property Site Tour Transport',
                'Maintenance' => 'Office Cleaning and Servicing',
                'Office' => 'Stationery and Printer Supplies',
                'Miscellaneous' => 'Refreshments for Client Meetings',
            ];
            $amounts = [
                'Marketing' => 15000.00,
                'Travel' => 3500.00,
                'Maintenance' => 5000.00,
                'Office' => 2500.00,
                'Miscellaneous' => 1200.00,
            ];

            foreach ($categories as $cat) {
                Expense::create([
                    'expense_title' => $titles[$cat],
                    'expense_category' => $cat,
                    'amount' => $amounts[$cat],
                    'expense_date' => now()->subDays(rand(1, 10)),
                    'notes' => 'Recurring operational monthly expense for ' . strtolower($cat),
                    'created_by' => 'Admin User',
                ]);
            }
        }
    }
}
