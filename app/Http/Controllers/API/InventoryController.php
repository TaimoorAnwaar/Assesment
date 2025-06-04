<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::with('product');

        if ($request->has('location')) {
            $query->where('location', $request->location);
        }

        return response()->json($query->paginate(10));
    }

    public function show($id)
    {
        $inventory = Inventory::with('product')->findOrFail($id);
        return response()->json($inventory);
    }

    public function updateQuantity(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer']);

        $inventory = Inventory::findOrFail($id);
        $inventory->quantity = $request->quantity;
        $inventory->save();

        return response()->json(['message' => 'Quantity updated', 'data' => $inventory]);
    }
}
