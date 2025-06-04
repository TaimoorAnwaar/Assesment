<?php
namespace App\Services;

use App\Models\Product;
use Carbon\Carbon;

class PricingEngineService
{
    public function calculatePrice(Product $product, int $quantity): float
    {
        $basePrice = $product->inventory->cost;
        $now = Carbon::now();
        $finalDiscount = 0;

        foreach ($product->pricingRules->sortByDesc('precedence') as $rule) {
            if ($rule->valid_from && $now->lt($rule->valid_from)) continue;
            if ($rule->valid_to && $now->gt($rule->valid_to)) continue;

            if ($rule->type === 'quantity' && $this->matchQuantityCondition($quantity, $rule->condition)) {
                $finalDiscount += $rule->discount;
            }

            if ($rule->type === 'time' && $this->matchTimeCondition($rule->condition, $now)) {
                $finalDiscount += $rule->discount;
            }
        }

        // Max discount 100%
        $finalDiscount = min($finalDiscount, 100);
        $finalPrice = $basePrice * (1 - $finalDiscount / 100);

        return round($finalPrice, 2);
    }

    private function matchQuantityCondition($quantity, $condition): bool
    {
        return eval("return $quantity $condition;");
    }

    private function matchTimeCondition($condition, Carbon $now): bool
    {
        if (strtolower($condition) === strtolower($now->format('l'))) {
            return true;
        }

        if (preg_match('/(\d{2}:\d{2})-(\d{2}:\d{2})/', $condition, $matches)) {
            $start = Carbon::createFromFormat('H:i', $matches[1]);
            $end = Carbon::createFromFormat('H:i', $matches[2]);
            return $now->between($start, $end);
        }

        return false;
    }
}
