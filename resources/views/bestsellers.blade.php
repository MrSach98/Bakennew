@include('userheader')

@section('title', 'Bestseller Cakes | Order Online')
@section('meta_description', 'Shop our most loved and top-selling cakes, ordered by thousands of happy customers.')

<div class="container-fluid px-4 px-lg-5 py-4">
    <nav class="mb-3">
        <small class="text-muted">
            <a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a> /
            <span class="text-dark fw-semibold">Bestsellers</span>
        </small>
    </nav>

    <h1 class="mb-1" style="font-size:1.6rem;">Bestseller Cakes</h1>
    <p class="text-muted mb-4">{{ $products->total() }} bestsellers found</p>

    <div class="row">
        <!-- Filter Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ url()->current() }}" id="filterForm">
                        <h6 class="fw-bold mb-3">Filters</h6>

                        <div class="mb-4">
                            <h6 class="small text-uppercase text-muted mb-2">Egg Preference</h6>
                            @foreach (['eggless' => 'Eggless', 'egg' => 'With Egg'] as $val => $label)
                                <div class="form-check">
                                    <input type="radio" name="egg_type" value="{{ $val }}" class="form-check-input filter-input"
                                           id="egg{{ $val }}" {{ request('egg_type') === $val ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="egg{{ $val }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <h6 class="small text-uppercase text-muted mb-2">Price Range (₹)</h6>
                            <div class="d-flex gap-2">
                                <input type="number" name="min_price" class="form-control form-control-sm filter-input" placeholder="Min" value="{{ request('min_price') }}">
                                <input type="number" name="max_price" class="form-control form-control-sm filter-input" placeholder="Max" value="{{ request('max_price') }}">
                            </div>
                        </div>

                        <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary w-100">Clear Filters</a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-end mb-3">
                <select class="form-select form-select-sm" style="max-width:220px;" id="sortSelect">
                    <option value="">Sort By: Newest</option>
                    <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
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
                    <p class="text-muted">No bestseller cakes found right now.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@include('userfooter')

@push('scripts')
<script>
    document.querySelectorAll('.filter-input').forEach(input => {
        input.addEventListener('change', () => document.getElementById('filterForm').submit());
    });

    document.getElementById('sortSelect').addEventListener('change', function () {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', this.value);
        window.location.href = url.toString();
    });
</script>
@endpush