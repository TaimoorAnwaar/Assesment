<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\PricingEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function processSale(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.location' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $total = 0;
            $details = [];
            $pricingService = new PricingEngineService();

            foreach ($request->items as $item) {
                // Get product
                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new \Exception("Product ID {$item['product_id']} not found.");
                }

                // Get inventory based on location
                $inventory = Inventory::where('product_id', $product->id)
                                      ->where('location', $item['location'])
                                      ->first();

                if (!$inventory || $inventory->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product ID: {$product->id} at location: {$item['location']}");
                }

                // Load pricing rules manually
                $product->load('pricingRules');
                $product->setRelation('inventory', $inventory); // so pricing engine works

                // Calculate price
                $unitPrice = $pricingService->calculatePrice($product, $item['quantity']);
                $subtotal = $unitPrice * $item['quantity'];
                $total += $subtotal;

                // Update inventory
                $inventory->decrement('quantity', $item['quantity']);

                // Add to transaction details
                $details[] = [
                    'product_id' => $product->id,
                    'location' => $item['location'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ];
            }

            // Save transaction
            $transaction = Transaction::create([
                'type' => 'sale',
                'details' => $details,
                'total_amount' => $total,
            ]);

            DB::commit();

            return response()->json(['message' => 'Transaction successful', 'transaction' => $transaction]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}
