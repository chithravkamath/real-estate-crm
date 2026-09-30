<?php

namespace App\Http\Controllers;

use App\Models\Negotiation;
use App\Models\Deal;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NegotiationController extends Controller
{
    /**
     * Show the form for creating a new negotiation.
     */
    public function create($deal_id)
    {
        $deal = Deal::findOrFail($deal_id);
        return view('add-negotiation', compact('deal'));
    }

    /**
     * Store a newly created negotiation in storage.
     */
    public function store(Request $request, $deal_id)
    {
        $deal = Deal::findOrFail($deal_id);

        $data = $request->validate([
            'offered_price' => 'required|numeric|min:0',
            'counter_offer' => 'nullable|numeric|min:0',
            'negotiation_note' => 'nullable|string',
            'negotiation_date' => 'required|date|before_or_equal:today',
            'status' => 'required|string|in:Pending,Approved,Rejected,Countered',
        ], [
            'offered_price.numeric' => 'The offered price must be a number.',
            'offered_price.min' => 'The offered price must be at least 0.',
            'counter_offer.numeric' => 'The counter offer must be a number.',
            'counter_offer.min' => 'The counter offer must be at least 0.',
            'negotiation_date.before_or_equal' => 'The negotiation date cannot be in the future.',
        ]);

        $data['deal_id'] = $deal->id;

        $negotiation = Negotiation::create($data);

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Create',
            'module' => 'Negotiations',
            'description' => "Created negotiation log for Deal #{$deal->id} (Offered: ₹{$negotiation->offered_price})"
        ]);

        return redirect('/deal-details/' . $deal->id)->with('success', 'Negotiation record created successfully');
    }

    /**
     * Show the form for editing the specified negotiation.
     */
    public function edit($id)
    {
        $negotiation = Negotiation::findOrFail($id);
        $deal = $negotiation->deal;

        return view('edit-negotiation', compact('negotiation', 'deal'));
    }

    /**
     * Update the specified negotiation in storage.
     */
    public function update(Request $request, $id)
    {
        $negotiation = Negotiation::findOrFail($id);

        $data = $request->validate([
            'offered_price' => 'required|numeric|min:0',
            'counter_offer' => 'nullable|numeric|min:0',
            'negotiation_note' => 'nullable|string',
            'negotiation_date' => 'required|date|before_or_equal:today',
            'status' => 'required|string|in:Pending,Approved,Rejected,Countered',
        ], [
            'offered_price.numeric' => 'The offered price must be a number.',
            'offered_price.min' => 'The offered price must be at least 0.',
            'counter_offer.numeric' => 'The counter offer must be a number.',
            'counter_offer.min' => 'The counter offer must be at least 0.',
            'negotiation_date.before_or_equal' => 'The negotiation date cannot be in the future.',
        ]);

        $negotiation->update($data);

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Update',
            'module' => 'Negotiations',
            'description' => "Updated negotiation log #{$negotiation->id} for Deal #{$negotiation->deal_id}"
        ]);

        return redirect('/deal-details/' . $negotiation->deal_id)->with('success', 'Negotiation record updated successfully');
    }

    /**
     * Remove the specified negotiation from storage.
     */
    public function destroy($id)
    {
        $negotiation = Negotiation::findOrFail($id);
        $dealId = $negotiation->deal_id;
        $negotiationId = $negotiation->id;

        $negotiation->delete();

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Delete',
            'module' => 'Negotiations',
            'description' => "Deleted negotiation record #{$negotiationId} from Deal #{$dealId}"
        ]);

        return redirect('/deal-details/' . $dealId)->with('success', 'Negotiation record deleted successfully');
    }
}
