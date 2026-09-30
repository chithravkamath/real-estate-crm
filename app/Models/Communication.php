<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Communication extends Model
{
    protected $fillable = [
        'lead_id',
        'client_id',
        'communication_type',
        'communication_date',
        'notes',
        'created_by'
    ];

    /**
     * Get the associated Lead.
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the associated Client.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get resolved related parent contact name.
     */
    public function getRelatedNameAttribute()
    {
        if ($this->lead_id && $this->lead) {
            return $this->lead->name;
        }
        if ($this->client_id && $this->client) {
            return $this->client->name;
        }
        return 'N/A';
    }
}
