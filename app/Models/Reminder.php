<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = [
        'title',
        'reminder_date',
        'reminder_time',
        'related_type',
        'related_id',
        'notes',
        'status'
    ];

    /**
     * Get the related item instance.
     */
    public function getRelatedItemAttribute()
    {
        switch ($this->related_type) {
            case 'Lead':
                return \App\Models\Lead::find($this->related_id);
            case 'Client':
                return \App\Models\Client::find($this->related_id);
            case 'Site Visit':
                return \App\Models\SiteVisit::find($this->related_id);
            case 'Deal':
                return \App\Models\Deal::find($this->related_id);
            default:
                return null;
        }
    }

    /**
     * Get the clean readable name of the related item.
     */
    public function getRelatedNameAttribute()
    {
        $item = $this->related_item;
        if (!$item) {
            return "Deleted {$this->related_type} (ID: {$this->related_id})";
        }

        switch ($this->related_type) {
            case 'Lead':
            case 'Client':
                return $item->name;
            case 'Site Visit':
                return "{$item->client_name} - {$item->property_name}";
            case 'Deal':
                return "{$item->client_name} - {$item->property_name}";
            default:
                return "Unknown";
        }
    }

    /**
     * Get the associated Lead.
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'related_id')->where('related_type', 'Lead');
    }

    /**
     * Get the associated Client.
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'related_id')->where('related_type', 'Client');
    }
}
