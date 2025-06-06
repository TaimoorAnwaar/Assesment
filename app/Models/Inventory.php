<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'location',
        'cost',
        'lot_number',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

