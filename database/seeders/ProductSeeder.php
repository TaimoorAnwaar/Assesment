<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::insert([
            [
                'sku' => 'PROD001',
                'name' => 'Laptop',
                'description' => 'Gaming Laptop',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'PROD002',
                'name' => 'Keyboard',
                'description' => 'Mechanical Keyboard',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

