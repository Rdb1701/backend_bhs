<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Property extends Model
{
    /** @use HasFactory<\Database\Factories\PropertyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'address',
        'description',
        'price',
        'availability_status',
        'room_type',
        'photos',
        'amenities',
        'persons_per_room',
        'contact_number',
        'lat',
        'long'
    ];

    protected $casts = [
        'photos' => 'array',
        'amenities' => 'array',
    ];


    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function location()
    {
        return $this->hasOne(Location::class);
    }
}

