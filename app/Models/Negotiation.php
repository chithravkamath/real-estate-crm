<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Negotiation extends Model
{
    protected $fillable = [
        'deal_id',
        'offered_price',
        'counter_offer',
        'negotiation_note',
        'negotiation_date',
        'status',
    ];

    protected $casts = [
        'negotiation_date' => 'date',
    ];

    /**
     * Get the associated Deal.
     */
    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
