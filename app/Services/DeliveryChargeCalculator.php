<?php

namespace App\Services;

use App\Models\DeliveryOption;
use App\Models\ServiceablePincode;
use App\Models\SiteSetting;

class DeliveryChargeCalculator
{
    public static function calculate(float $subtotal, string $pincode, DeliveryOption $deliveryOption, $cartItems): float
    {
        $settings = SiteSetting::current();

        // ---------- 1. Base charge ----------
        $baseCharge = 0.0;

        // (a) product-level override wins
        $productOverride = collect($cartItems)
            ->map(fn ($item) => $item->product->delivery_charge_override ?? null)
            ->filter()
            ->max();

        if ($productOverride !== null) {
            $baseCharge = (float) $productOverride;
        } else {
            // (b) pincode-level charge (agar tumhare table me 'charge' column hai)
            $pincodeRecord = ServiceablePincode::where('pincode', $pincode)
                ->where('is_active', true)
                ->first();

            if ($pincodeRecord && isset($pincodeRecord->charge) && $pincodeRecord->charge !== null) {
                $baseCharge = (float) $pincodeRecord->charge;
            } else {
                // (c) fallback to default
                $baseCharge = (float) $settings->default_delivery_charge;
            }
        }

        // ---------- 2. Free-delivery threshold zeroes ONLY the base ----------
        $threshold = (float) ($settings->free_delivery_threshold ?? 0);
        if ($threshold > 0 && $subtotal >= $threshold) {
            $baseCharge = 0.0;
        }

        // ---------- 3. Premium surcharge ALWAYS applies ----------
        $extraCharge = (float) ($deliveryOption->extra_charge ?? 0);

        return round($baseCharge + $extraCharge, 2);
    }
}