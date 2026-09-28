<?php

namespace App\Http\Controllers\Account;

use App\Models\OrderItem;
use App\Models\Review;

class MyReviewsController extends AccountBaseController
{
    public function index()
    {
        $pendingItems = OrderItem::with(['product.primaryImage', 'product.images', 'order'])
            ->whereNotNull('product_id')
            ->whereHas('product')
            ->whereHas('order', fn ($q) => $q->where('user_id', auth()->id())->where('status', 'delivered'))
            ->whereDoesntHave('review')
            ->latest()
            ->get();

        $reviews = Review::with(['product', 'images'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('account.reviews.index', compact('pendingItems', 'reviews'));
    }
}