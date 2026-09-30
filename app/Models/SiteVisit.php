<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteVisit extends Model
{
    protected $fillable = [
        'client_id',
        'client_name',
        'property_name',
        'visit_date',
        'visit_time',
        'agent_name',
        'agent_id',
        'status',
        'notes',
    ];

    /**
     * Get the associated User/Agent.
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get the associated Client.
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    protected $casts = [
        'visit_date' => 'date',
        'visit_time' => 'string',
    ];
}
