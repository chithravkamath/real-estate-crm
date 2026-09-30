<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'status',
        'property_interest',
        'budget',
        'notes',
        'user_id',
    ];

    /**
     * Get the User linked to this Client.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the communications logged for this Client.
     */
    public function communications()
    {
        return $this->hasMany(Communication::class);
    }

    /**
     * Get the reminders logged for this Client.
     */
    public function reminders()
    {
        return $this->hasMany(Reminder::class, 'related_id')->where('related_type', 'Client');
    }

    /**
     * Get all billing/payment records for this Client.
     */
    public function billings()
    {
        return $this->hasMany(Billing::class);
    }

    /**
     * Get all site visit records for this Client.
     */
    public function siteVisits()
    {
        return $this->hasMany(SiteVisit::class);
    }
}
