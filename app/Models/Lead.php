<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
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
        'source',
        'status',
        'interested_property',
        'budget',
        'notes',
        'agent',
        'assigned_agent_id',
        'follow_up_date',
    ];
 
    protected $casts = [
        'follow_up_date' => 'date',
    ];
 
    /**
     * Get the assigned User/Agent.
     */
    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    /**
     * Get the communications logged for this Lead.
     */
    public function communications()
    {
        return $this->hasMany(Communication::class);
    }

    /**
     * Get the reminders logged for this Lead.
     */
    public function reminders()
    {
        return $this->hasMany(Reminder::class, 'related_id')->where('related_type', 'Lead');
    }
}
