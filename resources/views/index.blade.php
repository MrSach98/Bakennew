@include('userheader')
<main class="bakeon-home">
@if ($heroBanners->count())
<section id="homeHeroSlider" class="carousel slide reference-hero" data-bs-ride="carousel" data-bs-interval="4500" data-bs-pause="false">
    <div class="carousel-inner">
        @foreach ($heroBanners as $banner)
            @php
                $bannerLink = $banner->link_url ?: '#';
                if ($bannerLink !== '#' && !\Illuminate\Support\Str::startsWith($bannerLink, ['http://', 'https://', '/'])) {
                    $bannerLink = url('/' . ltrim($bannerLink, '/'));
                }
            @endphp
            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                <img src="{{ asset($banner->image) }}" alt="{{ $banner->title ?: 'Sweet Bakes banner' }}">
                @if ($bannerLink !== '#')<a class="hero-slide-link" href="{{ $bannerLink }}" aria-label="{{ $banner->title ?: 'View offer' }}"></a>@endif
                @if ($banner->title || $banner->subtitle || $banner->button_text)
                    <div class="reference-hero-copy admin-hero-copy">
                        @if ($banner->subtitle)<span>{{ $banner->subtitle }}</span>@endif
                        @if ($banner->title)<h1>{{ $banner->title }}</h1>@endif
                        @if ($banner->button_text && $bannerLink !== '#')<a href="{{ $bannerLink }}">{{ $banner->button_text }}</a>@endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    @if ($heroBanners->count() > 1)
        <div class="carousel-indicators">@foreach($heroBanners as $banner)<button type="button" data-bs-target="#homeHeroSlider" data-bs-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}" aria-current="{{ $loop->first ? 'true' : 'false' }}" aria-label="Slide {{ $loop->iteration }}"></button>@endforeach</div>
        <button class="carousel-control-prev" type="button" data-bs-target="#homeHeroSlider" data-bs-slide="prev" aria-label="Previous banner"><span class="carousel-control-prev-icon"></span></button>
        <button class="carousel-control-next" type="button" data-bs-target="#homeHeroSlider" data-bs-slide="next" aria-label="Next banner"><span class="carousel-control-next-icon"></span></button>
    @endif
</section>
@else
<section id="homeHeroSlider" class="carousel slide reference-hero" data-bs-ride="carousel" data-bs-interval="4500" data-bs-pause="false">
    <div class="carousel-inner">
        <div class="carousel-item active"><img src="{{ asset('images/bakeon-school-hero.png') }}" alt="Teachers day cake"><a class="hero-slide-link" href="{{ url('/theme-cakes') }}" aria-label="Shop Teachers Day cakes"></a><div class="reference-hero-copy"><span>CELEBRATE THE MENTORS</span><h1>TEACHERS<br><em>THE SWEET WAY</em></h1><a href="{{ url('/theme-cakes') }}">ORDER NOW</a><p>TEACHERS' DAY | 05<sup>TH</sup> SEP</p></div></div>
        <div class="carousel-item"><img src="{{ asset('images/bakeon-birthday-hero.png') }}" alt="Celebration cakes"><a class="hero-slide-link" href="{{ url('/birthday-cakes') }}" aria-label="Shop birthday cakes"></a><div class="reference-hero-copy"><span>BAKED FOR EVERY BOND</span><h1>SWEETEN<br><em>EVERY CELEBRATION</em></h1><a href="{{ url('/birthday-cakes') }}">SHOP CAKES</a><p>FRESHLY BAKED • DELIVERED WITH LOVE</p></div></div>
        <div class="carousel-item"><img src="{{ asset('images/bakeon-anniversary-hero.png') }}" alt="Designer cakes"><a class="hero-slide-link" href="{{ url('/designer-cakes') }}" aria-label="Shop designer cakes"></a><div class="reference-hero-copy"><span>MAKE MOMENTS MAGICAL</span><h1>DESIGNER<br><em>CAKES FOR YOU</em></h1><a href="{{ url('/designer-cakes') }}">EXPLORE NOW</a><p>PREMIUM DESIGNS | UNFORGETTABLE TASTE</p></div></div>
    </div>
    <div class="carousel-indicators">@for($i=0;$i<3;$i++)<button type="button" data-bs-target="#homeHeroSlider" data-bs-slide-to="{{ $i }}" class="{{ $i===0?'active':'' }}" aria-label="Slide {{ $i+1 }}"></button>@endfor</div>
    <button class="carousel-control-prev" type="button" data-bs-target="#homeHeroSlider" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
    <button class="carousel-control-next" type="button" data-bs-target="#homeHeroSlider" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
</section>
@endif

@if ($menuChips->count())
    <section class="home-menu">
        <div class="home-container">
            <div class="home-heading"><h2>Menu</h2><p>What will you wish for?</p></div>
            <div class="menu-row">
                @php $menuFallbacks = ['1787796345_IAS8MIIF.png','1787796345_ked4JJ3L.png','1787875548_IufI8QNg.webp']; @endphp
                    @foreach ($menuChips as $chip)
                        <a href="{{ url('/' . $chip->slug) }}" class="menu-card">
                            <span class="menu-image">
                                @if($chip->image)
                                    <img src="{{ asset($chip->image) }}" alt="{{ $chip->name }}">
                                @else
                                    <img src="{{ asset('userassets/products/' . $menuFallbacks[$loop->index % count($menuFallbacks)]) }}" alt="{{ $chip->name }}">
                                @endif
                            </span><strong>{{ $chip->name }}</strong>
                        </a>
                @endforeach
            </div>
        </div>
    </section>
@endif

<section class="celebration-showcase" aria-label="Celebration offers">
      @if ($celebrationBanners->count())
    <div class="celebration-carousel" id="celebrationCarousel">
        @foreach ($celebrationBanners as $banner)
                @php
                    $celebLink = $banner->link_url ?: '#';
                    if ($celebLink !== '#' && !\Illuminate\Support\Str::startsWith($celebLink, ['http://', 'https://', '/'])) {
                        $celebLink = url('/' . ltrim($celebLink, '/'));
                    }
                @endphp
        <article class="celebration-slide" data-index="{{ $loop->index }}">
            <img src="{{ asset($banner->image) }}" alt="{{ $banner->title ?: 'Celebration offer' }}">
            <div class="celebration-copy">
                @if ($celebLink !== '#')
                    <a href="{{ $celebLink }}">{{ $banner->button_text ?: 'Order Now' }} <i class="fa-solid fa-arrow-right"></i></a>
                @endif
            </div>
        </article>
        @endforeach
       
        @if ($celebrationBanners->count() > 1)
                <button class="celebration-arrow celebration-prev" type="button" aria-label="Previous offer"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="celebration-arrow celebration-next" type="button" aria-label="Next offer"><i class="fa-solid fa-chevron-right"></i></button>
                <div class="celebration-dots">
                    @foreach ($celebrationBanners as $banner)
                        <button type="button" data-slide="{{ $loop->index }}" aria-label="Offer {{ $loop->iteration }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</section>    

@if ($bestsellers->count())
<section class="bestseller-section">
    <div class="home-container">
        <div class="home-heading country-heading">
            <h2><span>✦</span> India Loves</h2>
            <p>Bestsellers from across the country</p>
        </div>
        <div class="product-row">
            @foreach ($bestsellers as $product)
                @php
                    $variant = $product->defaultVariant();
                    $price = $variant
                        ? ($variant->discount_price ?? $variant->price)
                        : ($product->discount_price ?? $product->base_price);

                    $rating = $product->avg_rating ? round($product->avg_rating, 1) : 0;
                    $reviewsCount = $product->reviews_count ?? 0;

                    // Format count: 1800 -> 1.8K, 1500000 -> 1.5M
                    if ($reviewsCount >= 1000000) {
                        $formattedCount = round($reviewsCount / 1000000, 1) . 'M';
                    } elseif ($reviewsCount >= 1000) {
                        $formattedCount = round($reviewsCount / 1000, 1) . 'K';
                    } else {
                        $formattedCount = $reviewsCount;
                    }
                @endphp

                <article class="reference-product">
                    <a href="{{ url('/product/'.$product->slug) }}" class="reference-product-image">
                        @if($product->primaryImage)
                            <img src="{{ asset($product->primaryImage->image_path) }}" alt="{{ $product->name }}">
                        @else
                            <img src="{{ asset('userassets/products/1787796345_IAS8MIIF.png') }}" alt="{{ $product->name }}">
                        @endif
                        <span class="veg-mark"><i></i></span>
                    </a>

                    <h3><a href="{{ url('/product/'.$product->slug) }}">{{ $product->name }}</a></h3>

                    <div class="product-meta">
                        <strong>₹{{ number_format($price, 0) }}</strong>
                        <a href="{{ url('/wishlist') }}">♡</a>
                    </div>

                    @if ($reviewsCount > 0)
                        <small><b>{{ $rating }} ★</b> ({{ $formattedCount }} Reviews)</small>
                    @else
                        <small><b>New</b> (No reviews yet)</small>
                    @endif
                </article>
            @endforeach
        </div>
        <a href="{{ url('/bestsellers') }}" class="view-all-link">VIEW ALL</a>
    </div>
</section>
@endif

<!-- <section class="cashback-strip home-container"><div class="cashback-logo">pay<span>tm</span></div><div><strong>Assured cashback up to Rs 300*</strong><small>Valid till <b>30th Sep 2026</b></small></div><span class="cashback-stars">✦</span></section> -->

@if ($promoStripBanner)<section class="promo-wrap home-container"><a href="{{ $promoStripBanner->link_url ?? '#' }}"><img src="{{ asset($promoStripBanner->image) }}" alt="{{ $promoStripBanner->title ?? 'Special offer' }}"></a></section>@endif

<section class="promise-section"><div class="home-container"><div class="home-heading"><h2>Our Promise</h2><p>There’s no secret spell—only honest, hard work!</p></div><div class="promise-row">
<div><i class="fa-solid fa-truck"></i><strong>ON-TIME DELIVERY</strong><small>Because no one likes late surprises.</small></div><div><i class="fa-solid fa-cake-candles"></i><strong>500+ DESIGNS</strong><small>Wishes come in all shapes and sizes.</small></div><div><i class="fa-solid fa-gift"></i><strong>MADE WITH LOVE</strong><small>Thoughtfully prepared for every celebration.</small></div><div><i class="fa-solid fa-bread-slice"></i><strong>BAKED FRESH</strong><small>Spreading smiles, one slice at a time.</small></div>
</div></div></section>

<section class="magic-wrap home-container">
    <div class="magic-card">
        <svg class="magic-ticket-art" viewBox="0 0 1920 700" role="img" aria-label="The Magical Ticket">
            <defs><linearGradient id="ticketGold"><stop stop-color="#ad8b12"/><stop offset=".55" stop-color="#d7bb50"/><stop offset="1" stop-color="#a88509"/></linearGradient><path id="ticketTitleCurve" d="M500 335 Q960 100 1420 335"/></defs>
            <path class="ticket-shell" d="M16 175 C16 92 88 38 180 102 Q255 15 335 102 Q410 15 490 102 Q565 15 645 102 Q720 15 800 102 Q875 15 955 102 Q1030 15 1110 102 Q1185 15 1265 102 Q1340 15 1420 102 Q1495 15 1575 102 Q1650 15 1730 102 C1822 38 1904 92 1904 175 L1904 525 C1904 608 1822 662 1730 598 Q1650 685 1575 598 Q1495 685 1420 598 Q1340 685 1265 598 Q1185 685 1110 598 Q1030 685 955 598 Q875 685 800 598 Q720 685 645 598 Q565 685 490 598 Q410 685 335 598 Q255 685 180 598 C88 662 16 608 16 525 Z"/>
            <g class="ticket-left-art"><path d="M276 92C220 170 220 245 278 300C324 244 326 170 292 104" fill="#ed738d"/><path d="M288 98C258 174 259 237 280 292" fill="none" stroke="#ffd2dc" stroke-width="14"/><path d="M278 299C244 342 206 355 175 382M278 300C315 345 330 389 370 414M175 382C135 420 145 466 196 482M196 482C248 498 262 535 312 548" fill="none" stroke="#fff4b6" stroke-width="2"/><g fill="#ffe629"><text x="235" y="360">✦</text><text x="145" y="420">✦</text><text x="190" y="480">✦</text><text x="285" y="525">✦</text><text x="355" y="555">✦</text></g></g>
            <g class="ticket-right-art"><g fill="#ffe629"><text x="1510" y="238">✦</text><text x="1560" y="210">✦</text><text x="1610" y="245">✦</text><text x="1660" y="205">✦</text><text x="1700" y="260">✦</text><text x="1575" y="280">✦</text><text x="1650" y="300">✦</text></g><g stroke="#f7d861" stroke-width="2"><path d="M1518 240L1510 410M1570 220L1528 410M1620 250L1545 410M1670 215L1560 410M1710 270L1575 410"/></g><path d="M1508 365Q1540 390 1510 420Q1485 450 1510 480Q1535 510 1505 535" fill="none" stroke="#5269ae" stroke-width="18"/><path d="M1532 365Q1564 390 1534 420Q1509 450 1534 480Q1559 510 1529 535" fill="none" stroke="#9aa8dd" stroke-width="13"/><circle cx="1575" cy="520" r="28" fill="#e90727"/><path d="M1577 493Q1612 455 1600 430" fill="none" stroke="#bc001c" stroke-width="3"/><path d="M1558 544L1532 585M1590 544L1618 580" stroke="#e86c85" stroke-width="8" stroke-linecap="round"/></g>
            <text class="ticket-title"><textPath href="#ticketTitleCurve" startOffset="50%" text-anchor="middle">THE MAGICAL TICKET</textPath></text>
            <text class="ticket-copy" x="960" y="390" text-anchor="middle"><tspan x="960">Add 3 reminders in your account.</tspan><tspan x="960" dy="48">Win offers worth Rs. 750</tspan></text>
        </svg>
        <button type="button" data-bs-toggle="modal" data-bs-target="#loginModal">UNLOCK NOW</button>
    </div>
</section>

@php $heartProducts = ($featured->count() ? $featured : $bestsellers)->take(7)->values(); @endphp
<section class="social-section">
    <div class="home-heading"><h2>What’s In Your Heart?</h2><p>A glimpse from our social world!</p></div>
    @if ($heartProducts->count())
    <div class="heart-coverflow" id="heartCoverflow" aria-label="Featured cake gallery">
        <button class="heart-slider-arrow heart-slider-prev" type="button" aria-label="Previous image"><i class="fa-solid fa-chevron-left"></i></button>
        <div class="heart-slider-stage">
            @foreach ($heartProducts as $product)
            <a class="heart-slide" data-index="{{ $loop->index }}" href="{{ url('/product/'.$product->slug) }}" aria-label="View {{ $product->name }}">
                @if($product->primaryImage)<img src="{{ asset($product->primaryImage->image_path) }}" alt="{{ $product->name }}">@else<img src="{{ asset('userassets/products/1787796345_ked4JJ3L.png') }}" alt="{{ $product->name }}">@endif
                <span class="heart-instagram"><i class="fa-brands fa-instagram"></i></span>
                <div class="heart-slide-caption"><strong>{{ $product->name }}</strong><small>Made for your special moments</small></div>
            </a>
            @endforeach
        </div>
        <button class="heart-slider-arrow heart-slider-next" type="button" aria-label="Next image"><i class="fa-solid fa-chevron-right"></i></button>
        <div class="heart-slider-dots" aria-label="Choose a slide">
            @foreach ($heartProducts as $product)<button type="button" data-slide="{{ $loop->index }}" aria-label="Show {{ $product->name }}"></button>@endforeach
        </div>
    </div>
    @endif
</section>
</main>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const slider = document.getElementById('heartCoverflow');
    if (!slider) return;
    const slides = Array.from(slider.querySelectorAll('.heart-slide'));
    const dots = Array.from(slider.querySelectorAll('.heart-slider-dots button'));
    let current = Math.floor(slides.length / 2);
    let autoplay;
    function render() {
        slides.forEach(function (slide, index) {
            let position = index - current;
            if (position > slides.length / 2) position -= slides.length;
            if (position < -slides.length / 2) position += slides.length;
            slide.dataset.position = Math.max(-4, Math.min(4, position));
            slide.classList.toggle('is-active', position === 0);
            slide.setAttribute('aria-hidden', position === 0 ? 'false' : 'true');
        });
        dots.forEach(function (dot, index) {
            dot.classList.toggle('is-active', index === current);
            dot.setAttribute('aria-current', index === current ? 'true' : 'false');
        });
    }
    function move(step) { current = (current + step + slides.length) % slides.length; render(); }
    function startAutoplay() {
        window.clearInterval(autoplay);
        if (slides.length > 1) autoplay = window.setInterval(function () { move(1); }, 4000);
    }
    slider.querySelector('.heart-slider-prev').addEventListener('click', function () { move(-1); startAutoplay(); });
    slider.querySelector('.heart-slider-next').addEventListener('click', function () { move(1); startAutoplay(); });
    slides.forEach(function (slide, index) {
        slide.addEventListener('click', function (event) {
            if (index !== current) { event.preventDefault(); current = index; render(); startAutoplay(); }
        });
    });
    dots.forEach(function (dot, index) {
        dot.addEventListener('click', function () { current = index; render(); startAutoplay(); });
    });
    slider.addEventListener('mouseenter', function () { window.clearInterval(autoplay); });
    slider.addEventListener('mouseleave', startAutoplay);
    slider.addEventListener('keydown', function (event) {
        if (event.key === 'ArrowLeft') move(-1);
        if (event.key === 'ArrowRight') move(1);
    });
    render(); startAutoplay();
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const carousel = document.getElementById('celebrationCarousel');
    if (!carousel) return;
    const slides = Array.from(carousel.querySelectorAll('.celebration-slide'));
    const dots = Array.from(carousel.querySelectorAll('.celebration-dots button'));
    let current = 0;
    let timer;
    function render() {
        slides.forEach(function (slide, index) {
            let position = index - current;
            if (position > 1) position -= slides.length;
            if (position < -1) position += slides.length;
            slide.dataset.position = position;
        });
        dots.forEach(function (dot, index) { dot.classList.toggle('is-active', index === current); });
    }
    function move(step) { current = (current + step + slides.length) % slides.length; render(); }
    function autoplay() { window.clearInterval(timer); timer = window.setInterval(function () { move(1); }, 5200); }
    carousel.querySelector('.celebration-prev').addEventListener('click', function () { move(-1); autoplay(); });
    carousel.querySelector('.celebration-next').addEventListener('click', function () { move(1); autoplay(); });
    dots.forEach(function (dot, index) { dot.addEventListener('click', function () { current = index; render(); autoplay(); }); });
    carousel.addEventListener('mouseenter', function () { window.clearInterval(timer); });
    carousel.addEventListener('mouseleave', autoplay);
    render(); autoplay();
});
</script>
@endpush
@include('userfooter')
