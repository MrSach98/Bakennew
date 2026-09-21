<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Flavor;
use App\Models\Occasion;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryPageController extends StorefrontController
{
   
    // public function show(string $slug, Request $request)
    // {
    //     // Slug aur name (slugified) dono se check karega
    //     $category = Category::where('is_active', true)
    //         ->where(function ($q) use ($slug) {
    //             $q->where('slug', $slug)
    //             ->orWhere('name', str_replace('-', ' ', $slug));
    //         })
    //         ->firstOrFail();

    //     $categoryIds = collect([$category->id]);
    //     foreach ($category->children as $sub) {
    //         $categoryIds->push($sub->id);
    //         foreach ($sub->children as $child) {
    //             $categoryIds->push($child->id);
    //         }
    //     }

    //     $query = Product::with(['primaryImage', 'images', 'weightVariants.weight', 'flavors', 'occasions'])
    //         ->where('status', 'active')
    //         ->where(function ($q) use ($categoryIds) {
    //             $q->whereIn('category_id', $categoryIds)
    //               ->orWhereIn('subcategory_id', $categoryIds)
    //               ->orWhereIn('child_category_id', $categoryIds);
    //         });

    //     if ($request->filled('flavor')) {
    //         $query->whereHas('flavors', fn ($q) => $q->whereIn('flavors.id', (array) $request->flavor));
    //     }

    //     if ($request->filled('egg_type')) {
    //         $query->where('egg_type', $request->egg_type);
    //     }

    //     if ($request->filled('occasion')) {
    //         $query->whereHas('occasions', fn ($q) => $q->whereIn('occasions.id', (array) $request->occasion));
    //     }

    //     if ($request->filled('min_price') || $request->filled('max_price')) {
    //         $query->whereHas('weightVariants', function ($q) use ($request) {
    //             if ($request->filled('min_price')) {
    //                 $q->where('price', '>=', $request->min_price);
    //             }
    //             if ($request->filled('max_price')) {
    //                 $q->where('price', '<=', $request->max_price);
    //             }
    //         });
    //     }

    //     match ($request->input('sort')) {
    //         'price_low' => $query->orderBy('base_price', 'asc'),
    //         'price_high' => $query->orderBy('base_price', 'desc'),
    //         'bestseller' => $query->orderByDesc('is_bestseller'),
    //         default => $query->latest(),
    //     };

    //     $products = $query->paginate(12)->withQueryString();

    //     $availableFlavors = Flavor::whereHas('products', function ($q) use ($categoryIds) {
    //         $q->whereIn('category_id', $categoryIds)
    //           ->orWhereIn('subcategory_id', $categoryIds)
    //           ->orWhereIn('child_category_id', $categoryIds);
    //     })->where('is_active', true)->orderBy('name')->get();

    //     $availableOccasions = Occasion::whereHas('products', function ($q) use ($categoryIds) {
    //         $q->whereIn('category_id', $categoryIds)
    //           ->orWhereIn('subcategory_id', $categoryIds)
    //           ->orWhereIn('child_category_id', $categoryIds);
    //     })->where('is_active', true)->orderBy('name')->get();

    //     return view('category', compact('category', 'products', 'availableFlavors', 'availableOccasions'));
    // }

    public function show(string $slug, Request $request)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $categoryIds = collect([$category->id]);
        foreach ($category->children as $sub) {
            $categoryIds->push($sub->id);
            foreach ($sub->children as $child) {
                $categoryIds->push($child->id);
            }
        }

        $query = Product::with(['primaryImage', 'images', 'weightVariants.weight', 'flavors', 'occasions'])
            ->withCount(['approvedReviews as reviews_count'])
            ->withAvg(['approvedReviews as avg_rating'], 'rating')
            ->where('status', 'active')
            ->where(function ($q) use ($categoryIds) {
                $q->whereIn('category_id', $categoryIds)
                  ->orWhereIn('subcategory_id', $categoryIds)
                  ->orWhereIn('child_category_id', $categoryIds);
            });

        if ($request->filled('flavor')) {
            $query->whereHas('flavors', fn ($q) => $q->whereIn('flavors.id', (array) $request->flavor));
        }

        if ($request->filled('egg_type')) {
            $query->where('egg_type', $request->egg_type);
        }

        if ($request->filled('occasion')) {
            $query->whereHas('occasions', fn ($q) => $q->whereIn('occasions.id', (array) $request->occasion));
        }

        if ($request->filled('category_filter')) {
            $filterIds = (array) $request->category_filter;
            $query->where(function ($q) use ($filterIds) {
                $q->whereIn('category_id', $filterIds)
                  ->orWhereIn('subcategory_id', $filterIds)
                  ->orWhereIn('child_category_id', $filterIds);
            });
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
            'bestseller' => $query->orderByDesc('is_bestseller'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        // Price range bounds — slider ke liye real min/max is category ke products se
        // $priceBounds = \App\Models\ProductWeight::whereHas('product', function ($q) use ($categoryIds) {
        //     $q->where('status', 'active')
        //       ->where(function ($qq) use ($categoryIds) {
        //           $qq->whereIn('category_id', $categoryIds)
        //              ->orWhereIn('subcategory_id', $categoryIds)
        //              ->orWhereIn('child_category_id', $categoryIds);
        //       });
        // })->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();

        // $priceMin = (int) floor($priceBounds->min_price ?? 0);
        // $priceMax = (int) ceil($priceBounds->max_price ?? 5000);
        $priceBounds = \App\Models\ProductWeight::whereHas('product', function ($q) use ($categoryIds) {
            $q->where('status', 'active')
            ->where(function ($qq) use ($categoryIds) {
                $qq->whereIn('category_id', $categoryIds)
                    ->orWhereIn('subcategory_id', $categoryIds)
                    ->orWhereIn('child_category_id', $categoryIds);
            });
        })
        ->selectRaw('
            MIN(COALESCE(discount_price, 0)) as min_price,
            MAX(price) as max_price
        ')
        ->first();

        $priceMin = (int) floor($priceBounds->min_price ?? 0);
        $priceMax = (int) ceil($priceBounds->max_price ?? 5000);

        // Sibling categories — same parent ke doosre categories, filter list ke liye
        $siblingCategories = $category->parent_id
            ? Category::where('parent_id', $category->parent_id)->where('is_active', true)->orderBy('name')->get()
            : Category::whereNull('parent_id')->where('is_active', true)->orderBy('name')->get();

        // Iss category ke apne subcategories bhi
        $childCategories = Category::where('parent_id', $category->id)->where('is_active', true)->orderBy('name')->get();

        $availableFlavors = Flavor::whereHas('products', function ($q) use ($categoryIds) {
            $q->whereIn('category_id', $categoryIds)
              ->orWhereIn('subcategory_id', $categoryIds)
              ->orWhereIn('child_category_id', $categoryIds);
        })->where('is_active', true)->orderBy('name')->get();

        $availableOccasions = Occasion::whereHas('products', function ($q) use ($categoryIds) {
            $q->whereIn('category_id', $categoryIds)
              ->orWhereIn('subcategory_id', $categoryIds)
              ->orWhereIn('child_category_id', $categoryIds);
        })->where('is_active', true)->orderBy('name')->get();

        return view('category', compact(
            'category', 'products', 'availableFlavors', 'availableOccasions',
            'priceMin', 'priceMax', 'siblingCategories', 'childCategories'
        ));
    }
}