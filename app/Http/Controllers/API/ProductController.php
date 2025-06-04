<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function show($id)
    {
        $product = Product::with(['inventory', 'pricingRules'])->findOrFail($id);
        return response()->json($product);
    }
}

