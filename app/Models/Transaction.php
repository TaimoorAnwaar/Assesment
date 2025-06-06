<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'details',
        'total_amount',
    ];

    protected $casts = [
        'details' => 'array',
    ];
}

