<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\PricingRule;

class PricingRuleSeeder extends Seeder
{
    public function run()
    {
        $product = Product::where('sku', 'PROD001')->first(); // Laptop

        if ($product) {
            PricingRule::insert([
                [
                    'product_id' => $product->id,
                    'type' => 'quantity',
                    'condition' => '>=10',
                    'discount' => 5.00,
                    'valid_from' => now(),
                    'valid_to' => now()->addDays(30),
                    'precedence' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'product_id' => $product->id,
                    'type' => 'time',
                    'condition' => 'Saturday',
                    'discount' => 10.00,
                    'valid_from' => now(),
                    'valid_to' => now()->addDays(30),
                    'precedence' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}

