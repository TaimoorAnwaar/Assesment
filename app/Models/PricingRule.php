<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PricingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'condition',
        'discount',
        'valid_from',
        'valid_to',
        'precedence',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
