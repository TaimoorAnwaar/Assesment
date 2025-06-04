<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\Product;

class InventorySeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();

        foreach ($products as $product) {
            Inventory::create([
                'product_id' => $product->id,
                'quantity' => rand(10, 50),
                'location' => 'Main Warehouse',
                'cost' => rand(1000, 5000),
                'lot_number' => 'LOT-' . strtoupper(uniqid()),
            ]);
        }
    }
}
