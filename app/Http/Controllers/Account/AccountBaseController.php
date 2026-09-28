<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\StorefrontController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

abstract class AccountBaseController extends StorefrontController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        if (! auth()->check()) {
            abort(redirect('/'));
        }

        View::share('accountUser', auth()->user());
        View::share('pendingReviewCount', \App\Models\OrderItem::whereNotNull('product_id')
    ->whereHas('product')
    ->whereHas('order', fn ($q) => $q->where('user_id', auth()->id())->where('status', 'delivered'))
    ->whereDoesntHave('review')
    ->count());
    }
}