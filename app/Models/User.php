<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'department',
        'joining_date',
        'status',
        'phone',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
        ];
    }

    public function getDepartmentAttribute($value): string
    {
        if ($value) {
            return $value;
        }

        return match ($this->role) {
            'admin' => 'IT',
            'agent' => 'Sales',
            'accountant' => 'Finance',
            'client' => 'Client',
            default => 'Team',
        };
    }

    public function getJoiningDateAttribute($value): string
    {
        if ($value) {
            return $value;
        }

        return optional($this->created_at)->format('Y-m-d') ?? 'N/A';
    }

    public function getStatusAttribute($value): string
    {
        return $value ?: 'Active';
    }

    public function getPhoneAttribute($value): string
    {
        return $value ?: 'N/A';
    }

    public function client()
    {
        return $this->hasOne(Client::class);
    }
}
