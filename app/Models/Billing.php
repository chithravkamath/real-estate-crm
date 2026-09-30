<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $fillable = [
        'client_name',
        'client_id',
        'invoice_number',
        'property_name',
        'agent_name',
        'payment_amount',
        'commission',
        'payment_date',
        'due_date',
        'payment_status',
        'payment_method',
        'notes',
        'advance_amount',
        'advance_paid_at',
        'final_amount',
        'final_paid_at',
        'due_amount',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'due_date' => 'date',
        'advance_paid_at' => 'datetime',
        'final_paid_at' => 'datetime',
    ];

    /**
     * Get the Client that owns this Billing record.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the total amount paid so far.
     */
    public function getTotalPaidAttribute()
    {
        return (floatval($this->advance_amount ?? 0) + floatval($this->final_amount ?? 0));
    }

    /**
     * Get the remaining due amount.
     */
    public function getRemainingDueAttribute()
    {
        $due = floatval($this->due_amount ?? floatval($this->payment_amount));
        return max($due, 0); // Never allow negative due
    }
}
