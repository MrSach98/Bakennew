@include('userheader')



<div class="container-fluid px-4 px-lg-5 py-3 product-detail-page">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
            @if ($product->category)
                <li class="breadcrumb-item"><a href="{{ url('/category/' . $product->category->slug) }}" class="text-decoration-none text-muted">{{ $product->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Left Column: Gallery with Zoom -->
        <div class="col-lg-6 product-gallery-column">
            <div class="row g-0 position-relative">
                <div class="col-12 col-xl-7">
                    <div class="product-main-img-box mb-3 text-center" id="zoomImageBox">
                        <img id="mainProductImage"
                             src="{{ $product->images->first() ? asset($product->images->first()->image_path) : 'https://placehold.co/600x600/FFF8F0/D8232A?text=Bakingo+Cake' }}"
                             alt="{{ $product->name }}" class="img-fluid" style="max-height: 500px; object-fit: cover; width: 100%; cursor: crosshair;">

                        @if ($product->egg_type === 'eggless')
                            <span class="badge position-absolute top-0 start-0 m-3" style="background: #008a00; font-size: 0.85rem;">
                                <i class="fa-solid fa-leaf me-1"></i> 100% Eggless
                            </span>
                        @endif

                        @php
                            $isWishlisted = auth()->check() && $product->wishlists()->where('user_id', auth()->id())->exists();
                        @endphp

                        <button type="button" class="btn btn-light rounded-circle position-absolute top-0 end-0 m-3 shadow-sm btn-wishlist-toggle"
                                style="width:40px; height:40px;" data-product-id="{{ $product->id }}">
                            <i class="fa-{{ $isWishlisted ? 'solid' : 'regular' }} fa-heart {{ $isWishlisted ? 'text-danger' : '' }}"></i>
                        </button>

                        <div id="zoomLens" class="zoom-lens"></div>
                    </div>

                    @if ($product->images->count() > 1)
                        <div class="product-thumb-rail-wrap">
                            <button type="button" class="thumb-scroll-arrow thumb-scroll-up" id="thumbScrollUp" aria-label="Previous images">
                                <i class="fa-solid fa-chevron-up"></i>
                            </button>
                            <div class="d-flex gap-2 product-thumb-rail" id="productThumbRail">
                                @foreach ($product->images as $key => $img)
                                    <img src="{{ asset($img->image_path) }}"
                                         class="thumb-img {{ $key === 0 ? 'active-thumb' : '' }}"
                                         width="70" height="70" style="object-fit: cover;"
                                         onclick="changeMainImage(this, '{{ asset($img->image_path) }}')">
                                @endforeach
                            </div>
                            <button type="button" class="thumb-scroll-arrow thumb-scroll-down" id="thumbScrollDown" aria-label="More images">
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </div>
                    @endif
                </div>

                <div class="col-xl-5 d-none d-xl-block">
                    <div id="zoomResultPane" class="zoom-result-pane"></div>
                </div>
            </div>
        </div>

        <!-- Right Column: Product Specs & Ordering -->
        <div class="col-lg-6 product-info-column">
            <h1 class="h3 fw-bold text-dark mb-1">{{ $product->name }}</h1>

            <!-- Description right under the name -->
            @if ($product->short_description || $product->description)
                @php $descriptionText = $product->short_description ?: $product->description; @endphp
                <div class="mb-2 product-description-inline">
                    <p class="text-muted small mb-0" id="descText">
                        <span id="descShort">{{ \Illuminate\Support\Str::limit($descriptionText, 135, '') }}</span><span id="descFull" hidden>{{ $descriptionText }}</span>
                        @if (mb_strlen($descriptionText) > 135)
                            <button type="button" class="read-more-link" id="readMoreBtn">Read more</button>
                            <button type="button" class="read-less-link" id="readLessBtn" hidden>Read less</button>
                        @endif
                    </p>
                </div>
            @endif

            <!-- Rating summary (static placeholder until Reviews module exists) -->
            <!-- <div class="mb-2">
                <span class="fw-semibold">4.8</span>
                <span style="color:#ffb800;">★★★★★</span>
                <span class="text-muted small">(Be the first to review)</span>
            </div> -->
            <!-- Rating summary -->
           <div class="mb-2">
                @if ($reviewCount > 0)
                    <span class="fw-semibold">{{ $avgRating }}</span>
                    <span style="color:#ffb800;">
                        @for ($i = 1; $i <= 5; $i++)
                            {{ $i <= round($avgRating) ? '★' : '☆' }}
                        @endfor
                    </span>
                    <span class="text-muted small">({{ $reviewCount }} {{ Str::plural('Review', $reviewCount) }})</span>
                @else
                    <span class="text-muted small">Be the first to review this product</span>
                @endif
            </div>

            <!-- Pricing Box -->
            <div class="d-flex align-items-baseline gap-2 mb-3 price-row">
                <span class="fs-2 fw-extrabold text-danger" id="displayPrice">
                    ₹{{ number_format($defaultVariant ? ($defaultVariant->discount_price ?? $defaultVariant->price) : $product->base_price, 0) }}
                </span>
                @if ($defaultVariant && $defaultVariant->discount_price)
                    <span class="text-muted text-decoration-line-through fs-6" id="strikePrice">
                        ₹{{ number_format($defaultVariant->price, 0) }}
                    </span>
                    <span class="badge bg-success small">OFF</span>
                @endif
                <span class="small text-muted ms-1">(Inclusive of all taxes)</span>
            </div>

            <hr class="my-3">

            <!-- Weight Selection -->
            @if ($variantsByWeight->count())
            <div class="mb-4 position-relative">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="fw-bold small text-uppercase text-secondary mb-0">1. Select Weight</label>
                    <span class="small fw-semibold serving-info-link" id="servingInfoToggle" role="button" tabindex="0" aria-expanded="false" aria-controls="servingPopover" style="color:var(--brand-red);">
                        Serving Information
                    </span>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    @foreach ($variantsByWeight as $weightId => $variants)
                        @php
                            $firstVar = $variants->first();
                            $weight = $firstVar->weight;
                            $hasBothEggTypes = $variants->pluck('egg_type')->unique()->count() > 1;
                        @endphp
                        <div class="variant-card weight-option {{ $loop->first ? 'active' : '' }}"
                             data-weight-id="{{ $weightId }}"
                             data-variant-id="{{ $firstVar->id }}"
                             data-price="{{ $firstVar->discount_price ?? $firstVar->price }}"
                             data-original-price="{{ $firstVar->price }}"
                             data-has-discount="{{ $firstVar->discount_price ? 'true' : 'false' }}"
                             data-has-both-egg-types="{{ $hasBothEggTypes ? 'true' : 'false' }}"
                             data-eggless-variant-id="{{ $variants->firstWhere('egg_type', 'eggless')->id ?? '' }}"
                             data-eggless-price="{{ $variants->firstWhere('egg_type', 'eggless')->discount_price ?? $variants->firstWhere('egg_type', 'eggless')->price ?? '' }}"
                             data-egg-variant-id="{{ $variants->firstWhere('egg_type', 'egg')->id ?? '' }}"
                             data-egg-price="{{ $variants->firstWhere('egg_type', 'egg')->discount_price ?? $variants->firstWhere('egg_type', 'egg')->price ?? '' }}">
                            <div class="fw-bold fs-6">{{ $weight->label }}</div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Serves {{ $weight->serves_range ?: '—' }}</small>
                        </div>
                    @endforeach
                </div>

                <div class="serving-popover" id="servingPopover">
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Serving Information</strong>
                        <button type="button" class="btn-close btn-close-sm" id="closeServingPopover"></button>
                    </div>
                    @foreach ($servingInfo as $w)
                        <div class="serving-row">
                            <span>{{ $w->label }}</span>
                            <span class="text-muted">{{ $w->serves_range ?: '—' }} People</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Egg Preference -->
            <div class="mb-4" id="eggPreferenceWrap" style="display:none;">
                <label class="fw-bold small text-uppercase text-secondary d-block mb-2">Egg Preference</label>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-dark btn-sm egg-toggle-btn active" data-egg-type="eggless">
                        <i class="fa-solid fa-leaf me-1"></i> Eggless
                    </button>
                    <button type="button" class="btn btn-outline-dark btn-sm egg-toggle-btn" data-egg-type="egg">
                        With Egg
                    </button>
                </div>
            </div>

            <!-- Flavor Selection -->
            <!-- @if ($product->flavors->count())
            <div class="mb-4 product-flavour-field">
                <label for="flavourSelect" class="d-block mb-2">Select Flavours</label>
                <div class="flavour-select-wrap">
                    <select id="flavourSelect" class="form-select">
                        @foreach ($product->flavors as $flavor)
                            <option value="{{ $flavor->id }}" {{ $flavor->pivot->is_default ? 'selected' : '' }}>{{ $flavor->name }}</option>
                        @endforeach
                    </select>
                    <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                </div>
            </div>
            @endif -->
            @if ($product->flavors->count())
                <div class="mb-4 product-flavour-field">
                    <label for="flavourSelect" class="d-block mb-2">Select Flavours</label>
                    <div class="flavour-select-wrap">
                        <select id="flavourSelect" class="form-select">
                            @foreach ($product->flavors as $flavor)
                                <option value="{{ $flavor->id }}"
                                        data-price-modifier="{{ $flavor->pivot->price_modifier ?? 0 }}"
                                        {{ $flavor->pivot->is_default ? 'selected' : '' }}>
                                    {{ $flavor->name }}
                                    @if (($flavor->pivot->price_modifier ?? 0) > 0)
                                        (+₹{{ number_format($flavor->pivot->price_modifier, 0) }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                    </div>
                </div>
            @endif

            <!-- Custom Messages & Photo -->
            @if ($product->is_message_enabled || $product->is_photo_cake)
            <div class="p-3 bg-light rounded-3 mb-4 border">
                @if ($product->is_message_enabled)
                <div class="mb-2">
                    <label for="cakeMessage" class="fw-bold small text-secondary mb-1">Message on Cake (Optional)</label>
                    <input type="text" id="cakeMessage" class="form-control form-control-sm"
                           maxlength="{{ $product->message_char_limit ?? 30 }}"
                           placeholder="e.g. Happy Birthday Rahul!">
                    <div class="text-end small text-muted" style="font-size:0.75rem;">
                        <span id="charCount">0</span>/{{ $product->message_char_limit ?? 30 }}
                    </div>
                </div>
                @endif

                @if ($product->is_photo_cake)
                <div>
                    <label for="cakePhoto" class="fw-bold small text-secondary mb-1">Upload Photo for Cake</label>
                    <input type="file" id="cakePhoto" class="form-control form-control-sm" accept="image/*">
                </div>
                @endif
            </div>
            @endif

            <!-- Delivery Availability Box -->
            <div class="delivery-box p-3 mb-4">
                <div class="fw-bold text-dark mb-2"><i class="fa-solid fa-truck-fast me-2 text-danger"></i>Check Delivery Availability</div>
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="text" id="deliveryPincode" class="form-control form-control-sm"
                               placeholder="Enter Pincode" maxlength="6" value="{{ session('selected_pincode') }}">
                    </div>
                    <div class="col-md-4">
                        <input type="date" id="deliveryDate" class="form-control form-control-sm"
                               min="{{ now()->format('Y-m-d') }}" value="{{ now()->addDay()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-outline-danger btn-sm w-100" id="checkDeliveryBtn">Check</button>
                    </div>
                </div>
                <div id="deliveryResult" class="mt-2"></div>
                <!-- <div class="delivery-slot-picker mt-3">
                    <label class="delivery-slot-title">Select Delivery Slot</label>
                    <div class="delivery-slot-grid">
                        <button type="button" class="delivery-slot-btn" data-slot="09:00 AM - 11:00 AM">09:00 AM - 11:00 AM</button>
                        <button type="button" class="delivery-slot-btn" data-slot="11:00 AM - 01:00 PM">11:00 AM - 01:00 PM</button>
                        <button type="button" class="delivery-slot-btn" data-slot="02:00 PM - 04:00 PM">02:00 PM - 04:00 PM</button>
                        <button type="button" class="delivery-slot-btn" data-slot="05:00 PM - 07:00 PM">05:00 PM - 07:00 PM</button>
                        <button type="button" class="delivery-slot-btn advance-booking-btn" id="advanceBookingBtn" data-slot="advance"><i class="fa-regular fa-calendar"></i> Advance Booking</button>
                    </div>
                    <div class="advance-booking-panel" id="advanceBookingPanel" hidden>
                        <div><label for="advanceDeliveryDate">Delivery Date</label><input type="date" id="advanceDeliveryDate" min="{{ now()->addDay()->format('Y-m-d') }}"></div>
                        <div><label for="advanceDeliveryTime">Delivery Timing</label><select id="advanceDeliveryTime"><option value="">Select timing</option><option value="09:00 AM - 11:00 AM">09:00 AM - 11:00 AM</option><option value="11:00 AM - 01:00 PM">11:00 AM - 01:00 PM</option><option value="02:00 PM - 04:00 PM">02:00 PM - 04:00 PM</option><option value="05:00 PM - 07:00 PM">05:00 PM - 07:00 PM</option></select></div>
                    </div>
                    <input type="hidden" id="selectedDeliverySlot" value="">
                </div> -->
                <div class="delivery-slot-picker mt-3">
                    @if ($productDeliveryOptions->count())
                        <label class="delivery-slot-title">Available Delivery Options</label>
                        <div class="delivery-slot-grid">
                            @foreach ($productDeliveryOptions as $option)
                                @php
                                    $windowLabel = $option->delivery_window_start && $option->delivery_window_end
                                        ? \Carbon\Carbon::parse($option->delivery_window_start)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($option->delivery_window_end)->format('h:i A')
                                        : $option->name;
                                @endphp
                                <button type="button" class="delivery-slot-btn" data-slot="{{ $option->name }}" data-delivery-option-id="{{ $option->id }}">
                                    {{ $option->name }}
                                    @if ($option->extra_charge > 0)
                                        <small>(+₹{{ number_format($option->extra_charge, 0) }})</small>
                                    @endif
                                    <small class="d-block text-muted" style="font-size:0.7rem;">{{ $windowLabel }}</small>
                                </button>
                            @endforeach
                            <button type="button" class="delivery-slot-btn advance-booking-btn" id="advanceBookingBtn" data-slot="advance">
                                <i class="fa-regular fa-calendar"></i> Advance Booking
                            </button>
                        </div>
                    @else
                        <p class="text-muted small">No special delivery options configured for this product — standard delivery applies.</p>
                    @endif

                    <div class="advance-booking-panel" id="advanceBookingPanel" hidden>
                        <div><label for="advanceDeliveryDate">Delivery Date</label><input type="date" id="advanceDeliveryDate" min="{{ now()->addDay()->format('Y-m-d') }}"></div>
                        <div>
                            <label for="advanceDeliveryTime">Delivery Timing</label>
                            <select id="advanceDeliveryTime">
                                <option value="">Select timing</option>
                                @foreach ($productDeliveryOptions as $option)
                                    <option value="{{ $option->name }}">{{ $option->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <input type="hidden" id="selectedDeliverySlot" value="">
                </div>
            </div>

            @if ($product->sku)
            <div class="sku-info-block">
                <span>SKU Number</span>
                <strong>{{ $product->sku }}</strong>
            </div>
            @endif

            <a class="customizing-help-bar" href="https://api.whatsapp.com/send?text={{ urlencode('Hi, I need help customizing ' . $product->name) }}" target="_blank" rel="noopener">
                <i class="fa-brands fa-whatsapp"></i>
                <span>Need help customizing?</span>
                <strong>CONNECT NOW</strong>
            </a>

            <!-- @php
                $reviewImages = $product->images->take(5);
                $reviewSamples = [
                    ['name'=>'Riya Sharma','city'=>'Delhi','text'=>'The cake looked beautiful and tasted fresh. Everyone loved it!'],
                    ['name'=>'Ankit Verma','city'=>'Noida','text'=>'Exactly the same as shown in the picture. Delivered safely and on time.'],
                    ['name'=>'Neha Gupta','city'=>'Gurugram','text'=>'Soft, delicious and perfectly decorated. A wonderful celebration cake.'],
                    ['name'=>'Rahul Singh','city'=>'Mumbai','text'=>'Great quality and flavour. The packaging was also very secure.'],
                ];
            @endphp
            <div class="review-summary product-review-summary">
                <h2>Ratings &amp; Reviews</h2>
                <div class="review-score-row"><strong>4.8/5</strong><span class="review-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span></div>
                @if ($reviewImages->count())
                    <div class="review-photo-row">
                        @foreach ($reviewImages as $reviewImage)
                            <button type="button" class="review-photo"><img src="{{ asset($reviewImage->image_path) }}" alt="Customer review of {{ $product->name }}"></button>
                        @endforeach
                        <span class="review-photo-count">+48</span>
                    </div>
                @endif
            </div> -->
            <div class="review-summary product-review-summary">
                <h2>Ratings &amp; Reviews</h2>
                @if ($reviewCount > 0)
                    <div class="review-score-row">
                        <strong>{{ $avgRating }}/5</strong>
                        <span class="review-stars">
                            @for ($i = 1; $i <= 5; $i++)
                                {{ $i <= round($avgRating) ? '★' : '☆' }}
                            @endfor
                        </span>
                    </div>
                    @if ($reviewPhotos->count())
                        <div class="review-photo-row">
                            @foreach ($reviewPhotos->take(5) as $photo)
                                <button type="button" class="review-photo"><img src="{{ asset($photo->image_path) }}" alt="Customer review photo"></button>
                            @endforeach
                            @if ($reviewPhotos->count() > 5)
                                <span class="review-photo-count">+{{ $reviewPhotos->count() - 5 }}</span>
                            @endif
                        </div>
                    @endif
                @else
                    <p class="text-muted small">No reviews yet — be the first to share your experience!</p>
                @endif
            </div>

            <!-- Desktop Add to Cart Buttons -->
            <div class="d-none d-md-flex flex-column buy-now-action" id="buyNowAction">
                <div class="earliest-delivery-bar">
                    <i class="fa-regular fa-clock"></i>
                    <span>Earliest Delivery: <strong>{{ $earliestDelivery }}</strong></span>
                </div>
                <div class="input-group" style="width: 120px;">
                    <button class="btn btn-outline-secondary" type="button" id="qtyMinus">-</button>
                    <input type="text" class="form-control text-center" id="qtyInput" value="1" readonly>
                    <button class="btn btn-outline-secondary" type="button" id="qtyPlus">+</button>
                </div>
                <button type="button" class="btn btn-danger btn-lg flex-grow-1 fw-bold" id="addToCartBtn">
                    Buy Now | <span id="buyNowPrice">₹{{ number_format($defaultVariant ? ($defaultVariant->discount_price ?? $defaultVariant->price) : $product->base_price, 0) }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Full Description -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold text-dark border-bottom pb-2">Product Description</h5>
                    <p class="text-muted mt-3" style="line-height: 1.7;">{{ $product->description }}</p>
                </div>
            </div>
        </div>
    </div>
    
   @if ($reviewCount > 0)
    <section class="product-reviews-section mt-5" id="productReviews">
        <div class="review-cards-wrap">
            <button type="button" class="review-arrow review-arrow-left" aria-label="Previous reviews">
                <i class="fa-solid fa-chevron-left"></i>
            </button>

            <div class="review-cards-track" id="reviewCardsTrack">
                @foreach ($product->approvedReviews as $review)
                    <article class="customer-review-card">
                        @if ($review->images->count())
                            <img class="review-card-photo"
                                 src="{{ asset($review->images->first()->image_path) }}"
                                 alt="Review photo by {{ $review->user->name }}">
                        @endif

                        <div class="review-stars">
                            @for ($i = 1; $i <= 5; $i++)
                                {{ $i <= $review->rating ? '★' : '☆' }}
                            @endfor
                        </div>

                        @if ($review->title)
                            <p class="fw-semibold mb-1">{{ $review->title }}</p>
                        @endif

                        @if ($review->comment)
                            <p>&ldquo;{{ $review->comment }}&rdquo;</p>
                        @endif

                        <strong>{{ $review->user->name }} <span><i class="fa-solid fa-circle-check"></i> Verified</span></strong>
                        <small>{{ $review->created_at->format('d M Y') }}</small>
                    </article>
                @endforeach
            </div>

            <button type="button" class="review-arrow review-arrow-right" aria-label="Next reviews">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>

        @if ($product->approvedReviews->count() > 4)
            <button type="button" class="review-view-all" id="reviewViewAll">View All</button>
        @endif
    </section>
@endif

    <!-- <section class="product-reviews-section mt-5" id="productReviews">
        <div class="review-cards-wrap">
            <button type="button" class="review-arrow review-arrow-left" aria-label="Previous reviews"><i class="fa-solid fa-chevron-left"></i></button>
            <div class="review-cards-track" id="reviewCardsTrack">
                @foreach ($reviewSamples as $key => $review)
                    <article class="customer-review-card">
                        @if ($reviewImages->count())
                            @php $cardImage = $reviewImages->values()->get($key % $reviewImages->count()); @endphp
                            <img class="review-card-photo" src="{{ asset($cardImage->image_path) }}" alt="Review photo">
                        @endif
                        <div class="review-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                        <p>&ldquo;{{ $review['text'] }}&rdquo;</p>
                        <a href="#">{{ $product->name }}</a>
                        <strong>{{ $review['name'] }} <span><i class="fa-solid fa-circle-check"></i> Verified</span></strong>
                        <small>{{ $review['city'] }} &nbsp;&bull;&nbsp; Occasion: Birthday</small>
                    </article>
                @endforeach
            </div>
            <button type="button" class="review-arrow review-arrow-right" aria-label="Next reviews"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
        <button type="button" class="review-view-all" id="reviewViewAll">View All</button>
    </section> -->

    <!-- Recently Viewed -->
    @if ($recentlyViewed->count())
    <div class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold text-dark mb-0">Recently Viewed</h4>
            <a href="{{ url('/') }}" class="small fw-semibold text-decoration-none text-danger">VIEW ALL</a>
        </div>
        <div class="product-carousel-wrap">
            <button type="button" class="carousel-arrow carousel-arrow-left" data-target="recentlyViewedTrack">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <div class="related-scroll" id="recentlyViewedTrack">
                @foreach ($recentlyViewed as $rv)
                    <div class="related-card">
                        @include('partials.product-card', ['product' => $rv])
                    </div>
                @endforeach
            </div>
            <button type="button" class="carousel-arrow carousel-arrow-right" data-target="recentlyViewedTrack">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </div>
    @endif

    <!-- You May Also Like -->
    @if ($relatedProducts->count())
    <div class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold text-dark mb-0">You May Also Like</h4>
            <a href="{{ url('/category/' . ($product->category->slug ?? '')) }}" class="small fw-semibold text-decoration-none text-danger">VIEW ALL</a>
        </div>
        <div class="product-carousel-wrap">
            <button type="button" class="carousel-arrow carousel-arrow-left" data-target="relatedProductsTrack">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <div class="related-scroll" id="relatedProductsTrack">
                @foreach ($relatedProducts as $related)
                    <div class="related-card">
                        @include('partials.product-card', ['product' => $related])
                    </div>
                @endforeach
            </div>
            <button type="button" class="carousel-arrow carousel-arrow-right" data-target="relatedProductsTrack">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </div>
    @endif
</div>

<!-- Mobile Fixed Bottom Action Bar -->
<div class="mobile-sticky-bar d-flex align-items-center justify-content-between d-md-none">
    <div>
        <small class="text-muted d-block" style="font-size:0.7rem;">Total Price</small>
        <span class="fw-bold fs-5 text-danger" id="mobileDisplayPrice">₹{{ number_format($defaultVariant ? ($defaultVariant->discount_price ?? $defaultVariant->price) : $product->base_price, 0) }}</span>
    </div>
    <button type="button" class="btn btn-danger px-4 fw-bold" id="mobileAddToCartBtn">
        Add To Cart
    </button>
</div>



<script>
document.addEventListener('DOMContentLoaded', function () {
    let selectedVariantId = {{ $defaultVariant->id ?? 'null' }};
    let selectedBasePrice = {{ $defaultVariant ? ($defaultVariant->discount_price ?? $defaultVariant->price) : $product->base_price }};
    let selectedFlavorId = {{ $product->flavors->where('pivot.is_default', true)->first()->id ?? 'null' }};
    let quantity = 1;
    let currentEggType = 'eggless';

    // ---------- Native product-panel scrolling (no forced page movement) ----------
    const buyNowAction = document.getElementById('buyNowAction');
    const productInfoColumn = document.querySelector('.product-info-column');
    const productTopRow = document.querySelector('.product-detail-page > .row.g-4');
    const mainProductBox = document.getElementById('zoomImageBox');
    function syncProductDetailsWithPage() {
        if (!productInfoColumn || !productTopRow || !mainProductBox) return;
        if (buyNowAction) buyNowAction.classList.remove('is-floating');
        productInfoColumn.classList.remove('has-floating-buy');

        if (window.innerWidth < 992) {
            productInfoColumn.style.removeProperty('height');
            productInfoColumn.scrollTop = 0;
            productTopRow.style.removeProperty('min-height');
            if (buyNowAction) {
                buyNowAction.classList.remove('is-panel-fixed');
                buyNowAction.style.removeProperty('top');
                buyNowAction.style.removeProperty('left');
                buyNowAction.style.removeProperty('width');
                buyNowAction.style.removeProperty('--panel-buy-left');
                buyNowAction.style.removeProperty('--panel-buy-width');
            }
            return;
        }

        const imageHeight = Math.round(mainProductBox.getBoundingClientRect().height);
        const categoryNav = document.querySelector('.category-nav');
        const navRect = categoryNav ? categoryNav.getBoundingClientRect() : null;
        const stickyTop = navRect && navRect.bottom > 0 && navRect.bottom < window.innerHeight
            ? Math.round(navRect.bottom + 10)
            : 12;
        productTopRow.style.setProperty('--product-sticky-top', stickyTop + 'px');
        productInfoColumn.style.height = imageHeight + 'px';
        productTopRow.style.removeProperty('min-height');
        if (buyNowAction) {
            const panelRect = productInfoColumn.getBoundingClientRect();
            const rowRect = productTopRow.getBoundingClientRect();
            const buttonHeight = buyNowAction.offsetHeight || 96;
            const panelVisible = rowRect.bottom > stickyTop + buttonHeight && rowRect.top < window.innerHeight;
            buyNowAction.classList.toggle('is-panel-fixed', panelVisible);
            const constrainedBottom = Math.max(12, window.innerHeight - rowRect.bottom + 12);
            buyNowAction.style.setProperty('--panel-buy-bottom', constrainedBottom + 'px');
            buyNowAction.style.setProperty('--panel-buy-left', panelRect.left + 'px');
            buyNowAction.style.setProperty('--panel-buy-width', panelRect.width + 'px');
        }
    }
    window.addEventListener('scroll', syncProductDetailsWithPage, { passive: true });
    window.addEventListener('resize', syncProductDetailsWithPage);
    if (window.ResizeObserver) new ResizeObserver(syncProductDetailsWithPage).observe(mainProductBox);
    requestAnimationFrame(syncProductDetailsWithPage);

    // ---------- Gallery: thumbnail click ----------
    window.changeMainImage = function (element, imageSrc) {
        const mainImg = document.getElementById('mainProductImage');
        if (mainImg) mainImg.src = imageSrc;
        document.querySelectorAll('.thumb-img').forEach(el => el.classList.remove('active-thumb'));
        element.classList.add('active-thumb');
    };

    const thumbRail = document.getElementById('productThumbRail');
    const thumbUp = document.getElementById('thumbScrollUp');
    const thumbDown = document.getElementById('thumbScrollDown');
    if (thumbRail && thumbUp && thumbDown) {
        const thumbWrap = thumbRail.closest('.product-thumb-rail-wrap');
        const galleryMain = document.getElementById('zoomImageBox');
        const updateThumbArrows = () => {
            if (thumbWrap && galleryMain && window.innerWidth > 600) {
                thumbWrap.style.height = galleryMain.getBoundingClientRect().height + 'px';
            } else if (thumbWrap) {
                thumbWrap.style.height = '';
            }
            const vertical = window.innerWidth > 600;
            const hasOverflow = vertical
                ? thumbRail.scrollHeight > thumbRail.clientHeight + 2
                : thumbRail.scrollWidth > thumbRail.clientWidth + 2;
            thumbUp.hidden = !vertical || !hasOverflow;
            thumbDown.hidden = !vertical || !hasOverflow;
            thumbUp.disabled = thumbRail.scrollTop <= 1;
            thumbDown.disabled = thumbRail.scrollTop + thumbRail.clientHeight >= thumbRail.scrollHeight - 1;
        };
        thumbUp.addEventListener('click', () => thumbRail.scrollBy({ top: -104, behavior: 'smooth' }));
        thumbDown.addEventListener('click', () => thumbRail.scrollBy({ top: 104, behavior: 'smooth' }));
        thumbRail.addEventListener('scroll', updateThumbArrows, { passive: true });
        window.addEventListener('resize', updateThumbArrows);
        if (galleryMain && window.ResizeObserver) {
            new ResizeObserver(updateThumbArrows).observe(galleryMain);
        }
        requestAnimationFrame(updateThumbArrows);
    }

    // ---------- Zoom lens ----------
    const zoomImageBox = document.getElementById('zoomImageBox');
    const mainImg = document.getElementById('mainProductImage');
    const zoomLens = document.getElementById('zoomLens');
    const zoomResultPane = document.getElementById('zoomResultPane');

    if (zoomImageBox && mainImg && zoomLens && zoomResultPane) {
        function initZoom() {
            zoomResultPane.style.backgroundImage = `url('${mainImg.src}')`;
        }
        initZoom();

        const imgObserver = new MutationObserver(initZoom);
        imgObserver.observe(mainImg, { attributes: true, attributeFilter: ['src'] });

        zoomImageBox.addEventListener('mouseenter', function () {
            if (window.innerWidth < 1200) return;
            zoomLens.style.display = 'block';
            zoomResultPane.style.display = 'block';
        });

        zoomImageBox.addEventListener('mouseleave', function () {
            zoomLens.style.display = 'none';
            zoomResultPane.style.display = 'none';
        });

        zoomImageBox.addEventListener('mousemove', function (e) {
            if (window.innerWidth < 1200) return;

            const rect = mainImg.getBoundingClientRect();
            const lensSize = 150;

            let x = e.clientX - rect.left - lensSize / 2;
            let y = e.clientY - rect.top - lensSize / 2;

            x = Math.max(0, Math.min(x, rect.width - lensSize));
            y = Math.max(0, Math.min(y, rect.height - lensSize));

            zoomLens.style.left = x + 'px';
            zoomLens.style.top = y + 'px';

            const ratioX = zoomResultPane.offsetWidth / lensSize;
            const ratioY = zoomResultPane.offsetHeight / lensSize;

            zoomResultPane.style.backgroundSize = (rect.width * ratioX) + 'px ' + (rect.height * ratioY) + 'px';
            zoomResultPane.style.backgroundPosition = `-${x * ratioX}px -${y * ratioY}px`;
        });
    }

    // ---------- Weight selection ----------
    const weightOptions = document.querySelectorAll('.weight-option');
    weightOptions.forEach(card => {
        card.addEventListener('click', function () {
            weightOptions.forEach(c => c.classList.remove('active'));
            this.classList.add('active');

            const hasBothEggTypes = this.dataset.hasBothEggTypes === 'true';
            const eggPreferenceWrap = document.getElementById('eggPreferenceWrap');

            if (hasBothEggTypes) {
                if (eggPreferenceWrap) eggPreferenceWrap.style.display = 'block';
                currentEggType = 'eggless';
                document.querySelectorAll('.egg-toggle-btn').forEach(b => b.classList.remove('active'));
                const egglessBtn = document.querySelector('.egg-toggle-btn[data-egg-type="eggless"]');
                if (egglessBtn) egglessBtn.classList.add('active');
                selectedVariantId = this.dataset.egglessVariantId;
                selectedBasePrice = parseFloat(this.dataset.egglessPrice);
            } else {
                if (eggPreferenceWrap) eggPreferenceWrap.style.display = 'none';
                selectedVariantId = this.dataset.variantId;
                selectedBasePrice = parseFloat(this.dataset.price);
            }

            const originalPrice = parseFloat(this.dataset.originalPrice);
            const hasDiscount = this.dataset.hasDiscount === 'true';
            const strikePriceEl = document.getElementById('strikePrice');
            if (strikePriceEl) {
                strikePriceEl.style.display = hasDiscount ? 'inline' : 'none';
                if (hasDiscount) strikePriceEl.textContent = '₹' + originalPrice.toLocaleString('en-IN');
            }

            updateTotalPrice();
        });
    });

    // ---------- Egg preference toggle ----------
    document.querySelectorAll('.egg-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.egg-toggle-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentEggType = this.dataset.eggType;

            const activeWeightCard = document.querySelector('.weight-option.active');
            if (!activeWeightCard) return;

            if (currentEggType === 'egg') {
                selectedVariantId = activeWeightCard.dataset.eggVariantId;
                selectedBasePrice = parseFloat(activeWeightCard.dataset.eggPrice);
            } else {
                selectedVariantId = activeWeightCard.dataset.egglessVariantId;
                selectedBasePrice = parseFloat(activeWeightCard.dataset.egglessPrice);
            }
            updateTotalPrice();
        });
    });

    // ---------- Flavor selection ----------
    document.querySelectorAll('.flavor-option').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.flavor-option').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            selectedFlavorId = this.dataset.flavorId;
        });
    });
    
    let selectedFlavorPriceModifier = {{ $product->flavors->where('pivot.is_default', true)->first()->pivot->price_modifier ?? 0 }};

    const flavourSelect = document.getElementById('flavourSelect');
    if (flavourSelect) {
        selectedFlavorId = flavourSelect.value;
        selectedFlavorPriceModifier = parseFloat(flavourSelect.options[flavourSelect.selectedIndex]?.dataset.priceModifier || 0);

        flavourSelect.addEventListener('change', function () {
            selectedFlavorId = this.value;
            selectedFlavorPriceModifier = parseFloat(this.options[this.selectedIndex].dataset.priceModifier || 0);
            updateTotalPrice();
        });
    }

    function updateTotalPrice() {
        const finalPrice = (selectedBasePrice + selectedFlavorPriceModifier) * quantity;
        const formatted = '₹' + finalPrice.toLocaleString('en-IN');
        const displayEl = document.getElementById('displayPrice');
        const mobileEl = document.getElementById('mobileDisplayPrice');
        const buyNowEl = document.getElementById('buyNowPrice');
        if (displayEl) displayEl.textContent = formatted;
        if (mobileEl) mobileEl.textContent = formatted;
        if (buyNowEl) buyNowEl.textContent = formatted;
    }
        // const flavourSelect = document.getElementById('flavourSelect');
    // if (flavourSelect) {
    //     selectedFlavorId = flavourSelect.value;
    //     flavourSelect.addEventListener('change', function () {
    //         selectedFlavorId = this.value;
    //     });
    // }

    // // ---------- Price display ----------
    // function updateTotalPrice() {
    //     const finalPrice = selectedBasePrice * quantity;
    //     const formatted = '₹' + finalPrice.toLocaleString('en-IN');
    //     const displayEl = document.getElementById('displayPrice');
    //     const mobileEl = document.getElementById('mobileDisplayPrice');
    //     const buyNowEl = document.getElementById('buyNowPrice');
    //     if (displayEl) displayEl.textContent = formatted;
    //     if (mobileEl) mobileEl.textContent = formatted;
    //     if (buyNowEl) buyNowEl.textContent = formatted;
    // }

    // ---------- Quantity ----------
    const qtyPlus = document.getElementById('qtyPlus');
    const qtyMinus = document.getElementById('qtyMinus');
    const qtyInput = document.getElementById('qtyInput');

    if (qtyPlus) qtyPlus.addEventListener('click', () => {
        quantity++;
        if (qtyInput) qtyInput.value = quantity;
        updateTotalPrice();
    });
    if (qtyMinus) qtyMinus.addEventListener('click', () => {
        if (quantity > 1) {
            quantity--;
            if (qtyInput) qtyInput.value = quantity;
            updateTotalPrice();
        }
    });

    // ---------- Message character count ----------
    const msgInput = document.getElementById('cakeMessage');
    if (msgInput) {
        msgInput.addEventListener('input', function () {
            const counter = document.getElementById('charCount');
            if (counter) counter.textContent = this.value.length;
        });
    }

    // ---------- Read more / Read less ----------
    const descText = document.getElementById('descText');
    const descShort = document.getElementById('descShort');
    const descFull = document.getElementById('descFull');
    const readMoreBtn = document.getElementById('readMoreBtn');
    const readLessBtn = document.getElementById('readLessBtn');
    if (descText && descShort && descFull && readMoreBtn && readLessBtn) {
        readMoreBtn.addEventListener('click', () => {
            descShort.hidden = true;
            descFull.hidden = false;
            readMoreBtn.hidden = true;
            readLessBtn.hidden = false;
        });
        readLessBtn.addEventListener('click', () => {
            descShort.hidden = false;
            descFull.hidden = true;
            readLessBtn.hidden = true;
            readMoreBtn.hidden = false;
        });
    }

    // ---------- Serving info popover ----------
    const servingToggle = document.getElementById('servingInfoToggle');
    const servingPopover = document.getElementById('servingPopover');
    if (servingToggle && servingPopover) {
        servingToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            servingPopover.classList.toggle('show');
            servingToggle.setAttribute('aria-expanded', servingPopover.classList.contains('show') ? 'true' : 'false');
        });

        const closeBtn = document.getElementById('closeServingPopover');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                servingPopover.classList.remove('show');
                servingToggle.setAttribute('aria-expanded', 'false');
            });
        }

        document.addEventListener('click', function (e) {
            if (!servingPopover.contains(e.target) && e.target !== servingToggle) {
                servingPopover.classList.remove('show');
                servingToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // ---------- Delivery pincode check ----------
    const checkDeliveryBtn = document.getElementById('checkDeliveryBtn');
    if (checkDeliveryBtn) {
        checkDeliveryBtn.addEventListener('click', function () {
            const pincodeInput = document.getElementById('deliveryPincode');
            const resultBox = document.getElementById('deliveryResult');
            const pincode = pincodeInput ? pincodeInput.value.trim() : '';
            if (!pincode || !resultBox) return;

            fetch(`/check-pincode/${pincode}`)
                .then(res => res.json())
                .then(data => {
                    if (data.serviceable) {
                        resultBox.innerHTML = `<small class="text-success"><i class="fa-solid fa-circle-check me-1"></i>Awesome, we deliver to ${data.city}!</small>`;
                    } else {
                        resultBox.innerHTML = `<small class="text-danger"><i class="fa-solid fa-circle-xmark me-1"></i>Sorry, we don't deliver here yet.</small>`;
                    }
                });
        });
    }

    // ---------- Delivery slots and advance booking ----------
    const slotButtons = document.querySelectorAll('.delivery-slot-btn');
    const advanceBookingPanel = document.getElementById('advanceBookingPanel');
    const advanceDeliveryDate = document.getElementById('advanceDeliveryDate');
    const advanceDeliveryTime = document.getElementById('advanceDeliveryTime');
    const selectedDeliverySlot = document.getElementById('selectedDeliverySlot');
    const deliveryDateField = document.getElementById('deliveryDate');

    slotButtons.forEach(button => button.addEventListener('click', function () {
        slotButtons.forEach(item => item.classList.remove('active'));
        this.classList.add('active');
        const isAdvance = this.dataset.slot === 'advance';
        if (advanceBookingPanel) advanceBookingPanel.hidden = !isAdvance;
        requestAnimationFrame(() => requestAnimationFrame(syncProductDetailsWithPage));
        if (isAdvance) {
            if (selectedDeliverySlot) selectedDeliverySlot.value = '';
            if (advanceDeliveryDate) advanceDeliveryDate.focus();
        } else {
            if (selectedDeliverySlot) selectedDeliverySlot.value = this.dataset.slot;
            if (deliveryDateField) deliveryDateField.value = '{{ now()->format('Y-m-d') }}';
        }
    }));

    function syncAdvanceBooking() {
        if (advanceDeliveryDate && deliveryDateField) deliveryDateField.value = advanceDeliveryDate.value;
        if (selectedDeliverySlot && advanceDeliveryTime) selectedDeliverySlot.value = advanceDeliveryTime.value;
    }
    if (advanceDeliveryDate) advanceDeliveryDate.addEventListener('change', syncAdvanceBooking);
    if (advanceDeliveryTime) advanceDeliveryTime.addEventListener('change', syncAdvanceBooking);

    // ---------- Review carousel ----------
    const reviewTrack = document.getElementById('reviewCardsTrack');
    const reviewLeft = document.querySelector('.review-arrow-left');
    const reviewRight = document.querySelector('.review-arrow-right');
    const reviewViewAll = document.getElementById('reviewViewAll');
    const moveReviews = direction => {
        if (reviewTrack) reviewTrack.scrollBy({ left: direction * Math.max(300, reviewTrack.clientWidth * .72), behavior: 'smooth' });
    };
    if (reviewLeft) reviewLeft.addEventListener('click', () => moveReviews(-1));
    if (reviewRight) reviewRight.addEventListener('click', () => moveReviews(1));
    if (reviewViewAll && reviewTrack) reviewViewAll.addEventListener('click', function () {
        const expanded = reviewTrack.classList.toggle('show-all');
        this.textContent = expanded ? 'Show Less' : 'View All';
        if (reviewLeft) reviewLeft.hidden = expanded;
        if (reviewRight) reviewRight.hidden = expanded;
    });

    // ---------- Product carousels ----------
    document.querySelectorAll('.carousel-arrow').forEach(btn => {
        btn.addEventListener('click', function () {
            const track = document.getElementById(this.dataset.target);
            if (!track) return;

            const card = track.querySelector('.related-card');
            if (!card) return;

            const scrollAmount = card.offsetWidth + 16;
            const direction = this.classList.contains('carousel-arrow-left') ? -1 : 1;

            track.scrollBy({ left: scrollAmount * direction, behavior: 'smooth' });
        });
    });

    // ---------- Add to Cart ----------
    // function submitAddToCart() {
    //     const pincodeInput = document.getElementById('deliveryPincode');
    //     const pincode = pincodeInput ? pincodeInput.value.trim() : '';
    //     if (!pincode) {
    //         alert('Please enter your delivery pincode first!');
    //         if (pincodeInput) pincodeInput.focus();
    //         return;
    //     }

    //     const selectedSlotValue = selectedDeliverySlot ? selectedDeliverySlot.value : '';
    //     const advanceSelected = document.querySelector('.advance-booking-btn.active');
    //     if (!selectedSlotValue) {
    //         alert(advanceSelected ? 'Please select advance booking date and timing.' : 'Please select a delivery slot.');
    //         return;
    //     }
    //     if (advanceSelected && (!advanceDeliveryDate?.value || !advanceDeliveryTime?.value)) {
    //         alert('Please select both delivery date and timing.');
    //         return;
    //     }

    //     const dateInput = document.getElementById('deliveryDate');

    //     const payload = {
    //         product_id: {{ $product->id }},
    //         variant_id: selectedVariantId,
    //         flavor_id: selectedFlavorId,
    //         quantity: quantity,
    //         delivery_date: dateInput ? dateInput.value : null,
    //         delivery_slot: selectedSlotValue,
    //         pincode: pincode,
    //         cake_message: msgInput ? msgInput.value : null
    //     };

    //     fetch('{{ route("cart.add") }}', {
    //         method: 'POST',
    //         headers: {
    //             'X-CSRF-TOKEN': '{{ csrf_token() }}',
    //             'Content-Type': 'application/json',
    //             'Accept': 'application/json',
    //         },
    //         body: JSON.stringify(payload),
    //     })
    //     .then(res => res.json())
    //     .then(data => {
    //         if (data.success) {
    //             const cartCountEl = document.getElementById('cartCount');
    //             if (cartCountEl) cartCountEl.textContent = data.cart_count;
    //             alert('Product added to cart successfully!');
    //         } else {
    //             alert(data.message || 'Error adding product to cart.');
    //         }
    //     })
    //     .catch(() => alert('Something went wrong. Please try again.'));
    // }
        // ---------- Add to Cart ----------
    function submitAddToCart() {
        const pincodeInput = document.getElementById('deliveryPincode');
        const pincode = pincodeInput ? pincodeInput.value.trim() : '';
        if (!pincode) {
            showToast('Please enter your delivery pincode first!', 'danger');
            if (pincodeInput) pincodeInput.focus();
            return;
        }

        const selectedSlotValue = selectedDeliverySlot ? selectedDeliverySlot.value : '';
        const advanceSelected = document.querySelector('.advance-booking-btn.active');
        if (!selectedSlotValue) {
            showToast(advanceSelected ? 'Please select advance booking date and timing.' : 'Please select a delivery slot.', 'danger');
            return;
        }
        if (advanceSelected && (!advanceDeliveryDate?.value || !advanceDeliveryTime?.value)) {
            showToast('Please select both delivery date and timing.', 'danger');
            return;
        }

        const dateInput = document.getElementById('deliveryDate');

        const payload = {
            product_id: {{ $product->id }},
            variant_id: selectedVariantId,
            flavor_id: selectedFlavorId,
            quantity: quantity,
            delivery_date: dateInput ? dateInput.value : null,
            delivery_slot: selectedSlotValue,
            pincode: pincode,
            cake_message: msgInput ? msgInput.value : null
        };

        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        })
        .then(res => res.json().then(body => ({ status: res.status, body })))
        .then(({ status, body }) => {
            if (body.success) {
                const cartCountEl = document.getElementById('cartCount');
                if (cartCountEl) cartCountEl.textContent = body.cart_count;
                showToast('Product added to cart successfully!', 'success');
            } else {
                showToast(body.message || 'Error adding product to cart.', 'danger');
            }
        })
        .catch(() => showToast('Something went wrong. Please try again.', 'danger'));
    }

    const addToCartBtn = document.getElementById('addToCartBtn');
    const mobileAddToCartBtn = document.getElementById('mobileAddToCartBtn');
    if (addToCartBtn) addToCartBtn.addEventListener('click', submitAddToCart);
    if (mobileAddToCartBtn) mobileAddToCartBtn.addEventListener('click', submitAddToCart);
    
    // ------------------------------
    // ✅ ADDED: Wishlist Toggle Code
    // ------------------------------
    // const wishlistBtn = document.querySelector('.btn-wishlist-toggle');
    // if (wishlistBtn) {
    //     wishlistBtn.addEventListener('click', function () {
    //         const btn = this;
    //         const productId = btn.dataset.productId;

    //         fetch('{{ route("wishlist.toggle") }}', {
    //             method: 'POST',
    //             headers: {
    //                 'X-CSRF-TOKEN': '{{ csrf_token() }}',
    //                 'Content-Type': 'application/json',
    //                 'Accept': 'application/json',
    //             },
    //             body: JSON.stringify({ product_id: productId }),
    //         })
    //         .then(res => res.json().then(data => ({ status: res.status, body: data })))
    //         .then(({ status, body }) => {
    //             if (status === 401 && body.requires_login) {
    //                 // Login modal kholo (jo header me already banaya hai)
    //                 const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    //                 loginModal.show();
    //                 return;
    //             }

    //             if (body.success) {
    //                 const icon = btn.querySelector('i');
    //                 if (body.added) {
    //                     icon.classList.remove('fa-regular');
    //                     icon.classList.add('fa-solid', 'text-danger');
    //                 } else {
    //                     icon.classList.remove('fa-solid', 'text-danger');
    //                     icon.classList.add('fa-regular');
    //                 }

    //                 const wishlistCountEl = document.getElementById('wishlistCount');
    //                 if (wishlistCountEl) wishlistCountEl.textContent = body.wishlist_count;
    //             }
    //         });
    //     });
    // }
        const wishlistBtn = document.querySelector('.btn-wishlist-toggle');
    if (wishlistBtn) {
        wishlistBtn.addEventListener('click', function () {
            const btn = this;
            const productId = btn.dataset.productId;

            fetch('{{ route("wishlist.toggle") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ product_id: productId }),
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                if (status === 401 && body.requires_login) {
                    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                    loginModal.show();
                    return;
                }

                if (body.success) {
                    const icon = btn.querySelector('i');
                    if (body.added) {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid', 'text-danger');
                        showToast('Added to wishlist!', 'success');
                    } else {
                        icon.classList.remove('fa-solid', 'text-danger');
                        icon.classList.add('fa-regular');
                        showToast('Removed from wishlist.', 'success');
                    }

                    const wishlistCountEl = document.getElementById('wishlistCount');
                    if (wishlistCountEl) wishlistCountEl.textContent = body.wishlist_count;
                }
            })
            .catch(() => showToast('Something went wrong. Please try again.', 'danger'));
        });
    }
});
</script>

@include('userfooter')
