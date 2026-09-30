<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $fillable = [
        'client_name',
        'client_id',
        'property_name',
        'property_id',
        'agent_name',
        'agent_id',
        'deal_amount',
        'commission_percentage',
        'commission_amount',
        'booking_date',
        'status',
        'payment_status',
        'notes',
        'booking_status',
        'booking_confirmed_at',
        'sale_status',
        'sale_confirmed_at',
    ];

    /**
     * Get the associated User/Agent.
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get the associated Property.
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the associated Client.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }


    protected $casts = [
        'booking_date' => 'date',
        'booking_confirmed_at' => 'datetime',
        'sale_confirmed_at' => 'datetime',
    ];

    /**
     * Get the negotiations logged for this Deal.
     */
    public function negotiations()
    {
        return $this->hasMany(Negotiation::class);
    }

    /**
     * Get the documents uploaded for this Deal.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
