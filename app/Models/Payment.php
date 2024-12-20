<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'photo'
    ];


    protected $casts = [
        'photo' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
