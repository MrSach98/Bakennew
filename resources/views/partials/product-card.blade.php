@php
    $variant = $product->defaultVariant();
    $displayPrice = $variant ? ($variant->discount_price ?? $variant->price) : ($product->discount_price ?? $product->base_price);
    $oldPrice = $variant ? ($variant->discount_price ? $variant->price : null) : ($product->discount_price ? $product->base_price : null);
    $image = $product->primaryImage ?? $product->images->first();
@endphp

<div class="col-6 col-md-4 col-lg-4 category-product-column">
    <article class="reference-product category-reference-product">
        <a href="{{ route('product.show', ['slug' => $product->slug]) }}" class="reference-product-image">
            @if ($image)
                <img src="{{ asset($image->image_path) }}" alt="{{ $product->name }}" loading="lazy">
            @else
                <div class="category-product-placeholder">Cake</div>
            @endif
            <span class="veg-mark" aria-label="Vegetarian"><i></i></span>
            @if ($product->is_bestseller)
                <span class="category-bestseller">Best Seller</span>
            @endif
        </a>

        <h3><a href="{{ route('product.show', ['slug' => $product->slug]) }}">{{ $product->name }}</a></h3>
        <div class="product-meta">
            <div class="category-price">
                <strong>₹{{ number_format($displayPrice, 0) }}</strong>
                @if ($oldPrice)<del>₹{{ number_format($oldPrice, 0) }}</del>@endif
            </div>
            <button type="button" class="category-card-heart btn-wishlist-toggle" data-product-id="{{ $product->id }}" aria-label="Add {{ $product->name }} to wishlist">
                <i class="fa-regular fa-heart"></i>
            </button>
        </div>
        <small><b>4.9 <span>★</span></b> (1.8K Reviews)</small>
    </article>
</div>
