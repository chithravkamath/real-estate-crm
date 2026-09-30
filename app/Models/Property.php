<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'property_name',
        'property_type',
        'price',
        'location',
        'description',
        'area',
        'year_built',
        'bedrooms',
        'bathrooms',
        'garages',
        'amenities',
        'status',
        'owner_name',
        'owner_phone',
        'owner_email',
        'image',
        'document',
        'video',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amenities' => 'array',
        'price' => 'decimal:2',
        'area' => 'integer',
        'year_built' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'garages' => 'integer',
        'image' => 'string',
        'document' => 'string',
        'video' => 'string',
    ];

    /**
     * Get the documents uploaded for this Property.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get property title (alias for property_name).
     */
    public function getTitleAttribute()
    {
        return $this->property_name;
    }
}
