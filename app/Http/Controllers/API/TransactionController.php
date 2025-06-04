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
        ]);

        DB::beginTransaction();
        try {
            $total = 0;
            $details = [];
            $pricingService = new PricingEngineService();

            foreach ($request->items as $item) {
                $product = Product::with('inventory', 'pricingRules')->find($item['product_id']);

                if (!$product || !$product->inventory || $product->inventory->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product ID: {$item['product_id']}");
                }

                // Use Pricing Engine
                $unitPrice = $pricingService->calculatePrice($product, $item['quantity']);
                $subtotal = $unitPrice * $item['quantity'];
                $total += $subtotal;

                // Update inventory
                $product->inventory->decrement('quantity', $item['quantity']);

                $details[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ];
            }

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
