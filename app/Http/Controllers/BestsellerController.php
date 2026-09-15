<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class BestsellerController extends StorefrontController
{
    public function index(Request $request)
    {
        $query = Product::with(['primaryImage', 'images', 'weightVariants'])
            ->withCount(['approvedReviews as reviews_count'])
            ->withAvg(['approvedReviews as avg_rating'], 'rating')
            ->where('status', 'active')
            ->where('is_bestseller', true);

        // Same filters jo category page pe hain
        if ($request->filled('egg_type')) {
            $query->where('egg_type', $request->egg_type);
        }

        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('weightVariants', function ($q) use ($request) {
                if ($request->filled('min_price')) {
                    $q->where('price', '>=', $request->min_price);
                }
                if ($request->filled('max_price')) {
                    $q->where('price', '<=', $request->max_price);
                }
            });
        }

        match ($request->input('sort')) {
            'price_low' => $query->orderBy('base_price', 'asc'),
            'price_high' => $query->orderBy('base_price', 'desc'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        return view('bestsellers', compact('products'));
    }
}