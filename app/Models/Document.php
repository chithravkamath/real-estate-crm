<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'deal_id',
        'property_id',
        'document_name',
        'document_type',
        'file_path',
        'uploaded_by',
    ];

    /**
     * Get the associated Deal.
     */
    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    /**
     * Get the associated Property.
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
