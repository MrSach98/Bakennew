@php
    $variant = $product->defaultVariant();
    $displayPrice = $variant ? ($variant->discount_price ?? $variant->price) : ($product->discount_price ?? $product->base_price);
    $oldPrice = $variant ? ($variant->discount_price ? $variant->price : null) : ($product->discount_price ? $product->base_price : null);
    $image = $product->primaryImage ?? $product->images->first();
    $isWishlisted = auth()->check()
        && $product->wishlists()->where('user_id', auth()->id())->exists();

    // Reviews — controller me withCount/withAvg se aate hain (category page)
    // ya avg_rating/reviews_count attribute (home bestsellers)
    $rating = $product->avg_rating ?? 0;
    $reviewsCount = $product->reviews_count ?? 0;

    if ($reviewsCount >= 1000000) {
        $formattedCount = round($reviewsCount / 1000000, 1) . 'M';
    } elseif ($reviewsCount >= 1000) {
        $formattedCount = round($reviewsCount / 1000, 1) . 'K';
    } else {
        $formattedCount = $reviewsCount;
    }
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
             <button type="button"
                    class="category-card-heart js-card-wishlist"
                    data-product-id="{{ $product->id }}"
                    aria-label="{{ $isWishlisted ? 'Remove from wishlist' : 'Add to wishlist' }}">
                <i class="fa-{{ $isWishlisted ? 'solid' : 'regular' }} fa-heart {{ $isWishlisted ? 'text-danger' : '' }}"></i>
            </button>
        </div>

        {{-- ✅ Real reviews --}}
        @if ($reviewsCount > 0)
            <small><b>{{ round($rating, 1) }} <span>★</span></b> ({{ $formattedCount }} {{ \Illuminate\Support\Str::plural('Review', $reviewsCount) }})</small>
        @else
            <small><b>New</b> (No reviews yet)</small>
        @endif
    </article>
</div>
