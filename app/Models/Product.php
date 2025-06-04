<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'description',
    ];

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function pricingRules()
    {
        return $this->hasMany(PricingRule::class);
    }
}

