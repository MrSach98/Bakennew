


@include('userheader')

<style>
    .price-slider-wrap {
    position: relative;
    padding: 10px 4px 4px;
    height: 40px;
}
.price-slider-track {
    position: absolute;
    top: 20px;
    left: 4px;
    right: 4px;
    height: 5px;
    background: #e5e5e5;
    border-radius: 4px;
    z-index: 1;
}
.price-slider-range {
    position: absolute;
    height: 5px;
    background: #d8232a;
    border-radius: 4px;
    z-index: 2;
}
.price-slider-wrap input[type="range"] {
    position: absolute;
    top: 12px;
    left: 0;
    width: 100%;
    height: 20px;
    margin: 0;
    background: transparent;
    -webkit-appearance: none;
    appearance: none;
    pointer-events: none;
    z-index: 4;
}
.price-slider-wrap input[type="range"]::-webkit-slider-runnable-track {
    -webkit-appearance: none;
    background: transparent;
    height: 20px;
}
.price-slider-wrap input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    pointer-events: all;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #d8232a;
    border: 3px solid #fff;
    box-shadow: 0 0 4px rgba(0,0,0,0.4);
    cursor: pointer;
    margin-top: 0;
    position: relative;
    z-index: 5;
}
.price-slider-wrap input[type="range"]::-moz-range-track {
    background: transparent;
    height: 20px;
}
.price-slider-wrap input[type="range"]::-moz-range-thumb {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #d8232a;
    border: 3px solid #fff;
    box-shadow: 0 0 4px rgba(0,0,0,0.4);
    cursor: pointer;
    pointer-events: all;
    z-index: 5;
}
    .price-slider-values {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        font-weight: 600;
        color: #d8232a;
        margin-top: 4px;
    }
    .category-filter-list { max-height: 220px; overflow-y: auto; }
    .category-filter-list::-webkit-scrollbar { width: 5px; }
    .category-filter-list::-webkit-scrollbar-thumb { background: #ddd; border-radius: 4px; }
    .filter-section-title {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #888;
        margin-bottom: 10px;
        letter-spacing: 0.5px;
    }
</style>

<div class="container-fluid px-4 px-lg-5 py-4">
    <!-- Breadcrumb -->
    <nav class="mb-3">
        <small class="text-muted">
            <a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a> /
            <span class="text-dark fw-semibold">{{ $category->name }}</span>
        </small>
    </nav>

    <h1 class="mb-1" style="font-size:1.6rem;">{{ $category->name }}</h1>
    <p class="text-muted mb-4">{{ $products->total() }} cakes found</p>

    <div class="row">
        <!-- Filter Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ url()->current() }}" id="filterForm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">Filters</h6>
                            <a href="{{ url()->current() }}" class="small text-decoration-none text-muted">Clear All</a>
                        </div>

                        <!-- Category Filter -->
                        @if ($siblingCategories->count() || $childCategories->count())
                        <div class="mb-4">
                            <div class="filter-section-title">Category</div>
                            <div class="category-filter-list">
                                @if ($childCategories->count())
                                    @foreach ($childCategories as $child)
                                        <div class="form-check">
                                            <input type="checkbox" name="category_filter[]" value="{{ $child->id }}" class="form-check-input filter-input"
                                                   id="catf{{ $child->id }}" {{ in_array($child->id, (array) request('category_filter')) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="catf{{ $child->id }}">{{ $child->name }}</label>
                                        </div>
                                    @endforeach
                                @endif

                                @foreach ($siblingCategories as $sibling)
                                    <div class="form-check">
                                        <a href="{{ url($sibling->slug) }}" class="small text-decoration-none {{ $sibling->id === $category->id ? 'fw-bold text-danger' : 'text-dark' }} d-block py-1">
                                            {{ $sibling->name }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Price Range Slider -->
                        <div class="mb-4">
                            <div class="filter-section-title">Price Range (₹)</div>
                            <div class="price-slider-wrap">
                                <div class="price-slider-track">
                                    <div class="price-slider-range" id="priceSliderRange"></div>
                                </div>
                                <input type="range" id="priceMinSlider" min="{{ $priceMin }}" max="{{ $priceMax }}"
                                       value="{{ request('min_price', $priceMin) }}" step="10">
                                <input type="range" id="priceMaxSlider" min="{{ $priceMin }}" max="{{ $priceMax }}"
                                       value="{{ request('max_price', $priceMax) }}" step="10">
                            </div>
                            <div class="price-slider-values">
                                <span>₹<span id="priceMinLabel">{{ request('min_price', $priceMin) }}</span></span>
                                <span>₹<span id="priceMaxLabel">{{ request('max_price', $priceMax) }}</span></span>
                            </div>
                            <input type="hidden" name="min_price" id="minPriceInput" value="{{ request('min_price', $priceMin) }}">
                            <input type="hidden" name="max_price" id="maxPriceInput" value="{{ request('max_price', $priceMax) }}">
                        </div>

                        @if ($availableFlavors->count())
                            <div class="mb-4">
                                <div class="filter-section-title">Flavour</div>
                                @foreach ($availableFlavors as $flavor)
                                    <div class="form-check">
                                        <input type="checkbox" name="flavor[]" value="{{ $flavor->id }}" class="form-check-input filter-input"
                                               id="flavor{{ $flavor->id }}" {{ in_array($flavor->id, (array) request('flavor')) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="flavor{{ $flavor->id }}">{{ $flavor->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="mb-4">
                            <div class="filter-section-title">Egg Preference</div>
                            @foreach (['eggless' => 'Eggless', 'egg' => 'With Egg'] as $val => $label)
                                <div class="form-check">
                                    <input type="radio" name="egg_type" value="{{ $val }}" class="form-check-input filter-input"
                                           id="egg{{ $val }}" {{ request('egg_type') === $val ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="egg{{ $val }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>

                        @if ($availableOccasions->count())
                            <div class="mb-3">
                                <div class="filter-section-title">Occasion</div>
                                @foreach ($availableOccasions as $occasion)
                                    <div class="form-check">
                                        <input type="checkbox" name="occasion[]" value="{{ $occasion->id }}" class="form-check-input filter-input"
                                               id="occasion{{ $occasion->id }}" {{ in_array($occasion->id, (array) request('occasion')) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="occasion{{ $occasion->id }}">{{ $occasion->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <button type="submit" class="btn btn-dark btn-sm w-100 mt-2">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-end mb-3">
                <select class="form-select form-select-sm" style="max-width:220px;" id="sortSelect">
                    <option value="">Sort By: Relevance</option>
                    <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="bestseller" {{ request('sort') === 'bestseller' ? 'selected' : '' }}>Bestsellers</option>
                </select>
            </div>

            @if ($products->count())
                <div class="row g-4">
                    @foreach ($products as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fa-solid fa-cake-candles fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No cakes found matching your filters.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@push('scripts')
<script>
    // Checkbox/radio filters — auto-submit
    document.querySelectorAll('.filter-input').forEach(input => {
        input.addEventListener('change', () => document.getElementById('filterForm').submit());
    });

    document.getElementById('sortSelect').addEventListener('change', function () {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', this.value);
        window.location.href = url.toString();
    });

    // ---------- Dual-handle price slider ----------
    (function () {
        const minSlider = document.getElementById('priceMinSlider');
        const maxSlider = document.getElementById('priceMaxSlider');
        const rangeTrack = document.getElementById('priceSliderRange');
        const minLabel = document.getElementById('priceMinLabel');
        const maxLabel = document.getElementById('priceMaxLabel');
        const minInput = document.getElementById('minPriceInput');
        const maxInput = document.getElementById('maxPriceInput');

        const sliderMin = parseInt(minSlider.min, 10);
        const sliderMax = parseInt(minSlider.max, 10);
        const gap = 50; // min-max ke beech minimum farak

        function updateTrack() {
            const minVal = parseInt(minSlider.value, 10);
            const maxVal = parseInt(maxSlider.value, 10);

            const minPercent = ((minVal - sliderMin) / (sliderMax - sliderMin)) * 100;
            const maxPercent = ((maxVal - sliderMin) / (sliderMax - sliderMin)) * 100;

            rangeTrack.style.left = minPercent + '%';
            rangeTrack.style.right = (100 - maxPercent) + '%';

            minLabel.textContent = minVal;
            maxLabel.textContent = maxVal;
            minInput.value = minVal;
            maxInput.value = maxVal;
        }

        minSlider.addEventListener('input', function () {
            if (parseInt(maxSlider.value, 10) - parseInt(this.value, 10) < gap) {
                this.value = parseInt(maxSlider.value, 10) - gap;
            }
            updateTrack();
        });

        maxSlider.addEventListener('input', function () {
            if (parseInt(this.value, 10) - parseInt(minSlider.value, 10) < gap) {
                this.value = parseInt(minSlider.value, 10) + gap;
            }
            updateTrack();
        });

        // Apply Filters click hote hi min/max slider values hidden inputs me already set hain
        updateTrack();
    })();
</script>
@endpush
@include('userfooter')

