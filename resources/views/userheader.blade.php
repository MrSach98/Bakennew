<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', $siteSettings->store_name ?? 'Sweet Bakes') | Order Cakes Online</title>
    <meta name="description" content="@yield('meta_description', $siteSettings->default_meta_description ?? 'Order fresh cakes online with same day and midnight delivery.')">
    @php $browserFavicon = $siteSettings->favicon ?: 'images/bakeon-favicon.svg'; @endphp
    <link rel="icon" href="{{ asset($browserFavicon) }}?v=202609080425" type="{{ \Illuminate\Support\Str::endsWith($browserFavicon, '.svg') ? 'image/svg+xml' : 'image/png' }}">
    <link rel="shortcut icon" href="{{ asset($browserFavicon) }}?v=202609080425">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}?v=2">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}?v=2">
    <link rel="stylesheet" href="{{ asset('css/frontend.css') }}?v=2">
    <link rel="stylesheet" href="{{ asset('css/home-reference.css') }}?v=202609081805">
    <link rel="stylesheet" href="{{ asset('css/product-detail-reference.css') }}?v=2026090711435">

    

   
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const categoryNav = document.querySelector('.category-nav');
        if (categoryNav) {
            const placeholder = document.createElement('div');
            placeholder.className = 'category-nav-placeholder';
            categoryNav.parentNode.insertBefore(placeholder, categoryNav.nextSibling);
            let navStart = categoryNav.offsetTop;

            function updateFixedNav() {
                const shouldFix = window.scrollY >= navStart;
                if (shouldFix) placeholder.style.height = categoryNav.offsetHeight + 'px';
                categoryNav.classList.toggle('navbar-fixed-on-scroll', shouldFix);
                placeholder.classList.toggle('is-active', shouldFix);
            }

            window.addEventListener('scroll', updateFixedNav, { passive: true });
            window.addEventListener('resize', function () {
                if (!categoryNav.classList.contains('navbar-fixed-on-scroll')) navStart = categoryNav.offsetTop;
                updateFixedNav();
            });
            updateFixedNav();
        }

        const items = Array.from(document.querySelectorAll('.nav-item-mega'));
        items.forEach(function (item) {
            const trigger = item.querySelector(':scope > .nav-link');
            const menu = item.querySelector(':scope > .mega-dropdown');
            if (!trigger || !menu) return;
            trigger.setAttribute('aria-haspopup', 'true');
            trigger.setAttribute('aria-expanded', 'false');
            trigger.addEventListener('click', function (event) {
                event.preventDefault();
                const opening = !item.classList.contains('is-open');
                items.forEach(function (other) {
                    other.classList.remove('is-open');
                    const otherTrigger = other.querySelector(':scope > .nav-link');
                    if (otherTrigger) otherTrigger.setAttribute('aria-expanded', 'false');
                });
                item.classList.toggle('is-open', opening);
                trigger.setAttribute('aria-expanded', opening ? 'true' : 'false');
            });
            item.addEventListener('mouseleave', function () {
                item.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
            });
        });
        document.addEventListener('click', function (event) {
            if (event.target.closest('.nav-item-mega')) return;
            items.forEach(function (item) { item.classList.remove('is-open'); });
        });
    });
    </script>
</head>
<body>

<!-- Main header -->
<header class="main-header">
    <div class="container-fluid px-4 px-lg-5 py-4">
        <div class="row align-items-center gy-3 header-layout">
            <div class="col-6 col-lg-2 header-brand-cell">
                <a href="{{ url('/') }}" class="brand-logo {{ ($siteSettings->logo ?? null) ? 'has-logo' : '' }}">
                    @if ($siteSettings->logo ?? null)
                        <img src="{{ asset($siteSettings->logo) }}" alt="{{ $siteSettings->store_name }}" style="height:42px;">
                    @else
                        🎂 {{ $siteSettings->store_name ?? 'Bakeon' }}
                    @endif
                </a>
            </div>

            <div class="col-12 col-lg-5 order-3 order-lg-2 header-search-cell">
                <form action="{{ url('/search') }}" method="GET" class="search-box d-flex">
                    <button type="submit" class="px-4"><i class="fa-solid fa-magnifying-glass"></i></button>
                    <input type="text" name="q" class="form-control" placeholder="Search For Cakes, Occasion, Flavour And More...">
                </form>
            </div>

            <div class="col-6 col-lg-5 order-2 order-lg-3 header-actions-cell">
                <div class="d-flex justify-content-end align-items-center gap-4 header-actions">
                    @auth
                        <div class="dropdown">
                            <a href="javascript:void(0)" class="header-icon-btn dropdown-toggle" data-bs-toggle="dropdown" title="Account"><img class="header-icon-img" src="{{ asset('images/icons/user.svg') }}?v=5" alt="">
                                <svg class="header-fa-solid" viewBox="0 0 448 512" aria-hidden="true"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512h388.6c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304h-91.4z"/></svg><small>Hi, {{ \Illuminate\Support\Str::limit(auth()->user()->name, 10, '') }}</small>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width:220px;">
                                <li class="px-3 py-2 border-bottom">
                                    <div class="fw-bold">Hey {{ auth()->user()->name }}!</div>
                                    <small class="text-muted">{{ auth()->user()->email }}</small>
                                </li>
                                <li><a class="dropdown-item" href="{{ url('/account/orders') }}"><i class="fa-solid fa-box me-2"></i> My Orders</a></li>
                                <li><a class="dropdown-item" href="{{ url('/wishlist') }}"><i class="fa-regular fa-heart me-2"></i> My Favourites</a></li>
                                <li><a class="dropdown-item" href="{{ url('/account/addresses') }}"><i class="fa-solid fa-location-dot me-2"></i> Manage Address</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('auth.logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="javascript:void(0)" class="header-icon-btn" title="Login" data-bs-toggle="modal" data-bs-target="#loginModal"><img class="header-icon-img" src="{{ asset('images/icons/user.svg') }}?v=5" alt="">
                            <svg class="header-fa-solid" viewBox="0 0 448 512" aria-hidden="true"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512h388.6c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304h-91.4z"/></svg><small>Login/Signup</small>
                        </a>
                    @endauth

                    <a href="{{ url('/track-order') }}" class="header-icon-btn" title="Track Order"><svg class="header-fa-solid route-svg" viewBox="0 0 512 512" aria-hidden="true"><path d="M512 96c0 50.2-59.1 125.1-84.6 155-3.8 4.4-9.4 6.1-14.5 5H320c-17.7 0-32 14.3-32 32s14.3 32 32 32h96c53 0 96 43 96 96s-43 96-96 96H139.6c8.7-9.9 19.3-22.6 30-36.8 6.3-8.4 12.8-17.6 19-27.2H416c17.7 0 32-14.3 32-32s-14.3-32-32-32h-96c-53 0-96-43-96-96s43-96 96-96h39.8C338.8 160.5 320 124.3 320 96c0-53 43-96 96-96s96 43 96 96zM117.1 489.1c-3.8 4.3-7.2 8.1-10.1 11.3l-1.8 2-.2-.2c-6 4.6-14.6 4-20-1.8C59.8 473 0 402.5 0 352c0-53 43-96 96-96s96 43 96 96c0 30-21.1 67-43.5 97.9-10.7 14.7-21.7 28-30.8 38.5l-.6.7zM128 352a32 32 0 1 0-64 0 32 32 0 1 0 64 0zM416 128a32 32 0 1 0 0-64 32 32 0 1 0 0 64z"/></svg><small>Track Order</small></a>
                    <a href="{{ url('/cart') }}" class="header-icon-btn" title="Cart"><img class="header-icon-img cart-icon-img" src="{{ asset('images/icons/cart-shopping.svg') }}?v=5" alt="">
                        <svg class="header-fa-solid cart-svg" viewBox="0 0 576 512" aria-hidden="true"><path d="M0 24C0 10.7 10.7 0 24 0h45.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1-96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/></svg><small>Cart</small>
                        <span class="badge rounded-pill {{ ($cartCount ?? 0) > 0 ? '' : 'd-none' }}" id="cartCount">{{ $cartCount ?? 0 }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Category navigation strip -->
<nav class="category-nav">
    <div class="container-fluid px-4 px-lg-5 py-4">
        <div class="category-nav-scroll d-flex gap-2 reference-nav-links">
            @if(isset($headerCategories))
                @foreach ($headerCategories as $category)
                    @php
                        $hasSubcategories = $category->children->count() > 0;
                        $categoryUrl = url('/' . $category->slug);
                        $categoryActive = request()->is($category->slug);
                    @endphp

                    <div class="nav-item-mega">
                        <a href="{{ $hasSubcategories ? 'javascript:void(0);' : $categoryUrl }}"
                           class="nav-link {{ $hasSubcategories ? 'pe-none-custom' : '' }} {{ $categoryActive ? 'nav-highlight' : '' }}">
                            {{ $category->name }}
                        </a>

                        @if($hasSubcategories)
                            <div class="mega-dropdown">
                                @foreach($category->children as $subcategory)
                                    @php
                                        $hasChildren = $subcategory->children->count() > 0;
                                        $subCategoryUrl = url('/' . $subcategory->slug);
                                    @endphp

                                    <div class="mega-column">
                                        <a href="{{ $hasChildren ? 'javascript:void(0);' : $subCategoryUrl }}"
                                           class="subcat-title text-decoration-none {{ $hasChildren ? 'pe-none-custom' : '' }}">
                                            {{ $subcategory->name }}
                                        </a>

                                        @if($hasChildren)
                                            <ul class="child-menu-list">
                                                @foreach($subcategory->children as $child)
                                                    @php
                                                        $childUrl = url('/' . $child->slug);
                                                    @endphp
                                                    <li>
                                                        <a href="{{ $childUrl }}">
                                                            {{ $child->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</nav>

<!-- Login / Signup Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4">

                <!-- Step 1: Enter Email/Mobile -->
                <div id="loginStep1">
                    <h4 class="fw-bold mb-1">Login / Sign Up! 👋</h4>
                    <p class="text-muted mb-3" id="loginPromptText">Please enter your email address</p>

                    <div class="input-group mb-2">
                        <span class="input-group-text bg-white"><i class="fa-regular fa-envelope text-muted"></i></span>
                        <input type="email" id="loginEmailInput" class="form-control" placeholder="Your email address">
                    </div>

                    <!-- <div class="input-group mb-2 d-none">
                        <span class="input-group-text bg-white">+91</span>
                        <input type="text" id="loginPhoneInput" class="form-control" maxlength="10" placeholder="10 digit mobile number">
                    </div> -->

                    <!-- <div class="text-end mb-3">
                        <a href="javascript:void(0)" id="toggleMethodLink" class="small fw-semibold text-decoration-none" style="color:var(--brand-maroon);">With Mobile</a>
                    </div> -->

                    <div id="loginErrorBox" class="text-danger small mb-2"></div>

                    <button type="button" class="btn w-100 fw-bold" id="getOtpBtn" style="background:var(--brand-maroon); color:#fff;">
                        Get OTP <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>

                    <p class="small text-muted mt-3 mb-0">
                        By continuing I agree to {{ $siteSettings->store_name ?? 'Sweet Bakes' }}'s <a href="{{ url('/terms-and-conditions') }}">Terms &amp; Conditions</a> and <a href="{{ url('/privacy-policy') }}">Privacy Policy</a>.
                    </p>

                    <hr>
                    <p class="text-center small mb-0">
                        New here? <a href="javascript:void(0)" id="showSignupLink" class="fw-semibold text-decoration-none" style="color:var(--brand-maroon);">Create an Account</a>
                    </p>
                </div>

                <!-- Step 2: OTP Verification -->
                <div id="loginStep2" class="d-none">
                    <h4 class="fw-bold mb-2">OTP Verification</h4>
                    <p class="text-muted mb-3">
                        We have sent you a 6 digit OTP to <strong id="maskedContactDisplay"></strong>
                        (<a href="javascript:void(0)" id="editContactLink">Edit</a>)
                    </p>

                    <input type="text" id="otpInput" class="form-control text-center mb-2" maxlength="6" style="letter-spacing:6px; font-size:1.3rem;" placeholder="------">

                    <div class="text-center small text-muted mb-3">
                        <span id="resendTimerText">Resend OTP in <span id="resendCountdown">30</span>s</span>
                        <a href="javascript:void(0)" id="resendOtpLink" class="d-none fw-semibold text-decoration-none" style="color:var(--brand-maroon);">Resend OTP</a>
                    </div>

                    <div id="otpErrorBox" class="text-danger small mb-2"></div>

                    <button type="button" class="btn w-100 fw-bold" id="verifyOtpBtn" style="background:var(--brand-maroon); color:#fff;">
                        Verify OTP <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>
                </div>
              
                <div id="loginStep3" class="d-none">
                    <h4 class="fw-bold mb-1">Complete Your Profile</h4>
                    <p class="text-muted mb-3">Just a few more details to finish signing up</p>

                    <div class="mb-2">
                        <label class="form-label small">Full Name</label>
                        <input type="text" id="signupNameInput" class="form-control" placeholder="Your full name">
                    </div>

                    
                    <div class="mb-2 d-none" id="signupPhoneWrap">
                        <label class="form-label small">Mobile Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">+91</span>
                            <input type="text" id="signupPhoneInput" class="form-control" maxlength="10" placeholder="10 digit mobile number">
                        </div>
                    </div>

                    
                    <div class="mb-2 d-none" id="signupEmailWrap">
                        <label class="form-label small">Email Address</label>
                        <input type="email" id="signupEmailInput" class="form-control" placeholder="Your email address">
                    </div>

                    <div class="mb-2">
                        <label class="form-label small">Password</label>
                        <input type="password" id="signupPasswordInput" class="form-control" placeholder="Create a password" minlength="6">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Confirm Password</label>
                        <input type="password" id="signupConfirmPasswordInput" class="form-control" placeholder="Confirm your password" minlength="6">
                    </div>

                    <div id="signupErrorBox" class="text-danger small mb-2"></div>

                    <button type="button" class="btn w-100 fw-bold" id="completeSignupBtn" style="background:var(--brand-maroon); color:#fff;">
                        Create Account <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function checkPincode() {
        const pincode = document.getElementById('pincodeInput').value.trim();
        const resultBox = document.getElementById('pincodeResult');

        if (!pincode) return;

        fetch(`/check-pincode/${pincode}`)
            .then(res => res.json())
            .then(data => {
                if (data.serviceable) {
                    resultBox.innerHTML = `<div class="alert alert-success mb-0">
                        Delivering to <strong>${data.city}</strong>.
                        ${data.same_day_available ? '✅ Same Day' : ''}
                        ${data.midnight_available ? '✅ Midnight' : ''}
                        ${data.express_available ? '✅ Express' : ''}
                    </div>`;
                    document.getElementById('selectedCityLabel').textContent = data.city;
                } else {
                    resultBox.innerHTML = `<div class="alert alert-danger mb-0">Sorry, we don't deliver to this pincode yet.</div>`;
                }
            });
    }

    $(function () {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        let currentMethod = 'email';
        let resendInterval;
        let pendingUserId = null;
        let verifiedMethod = null;

        function showStep1() {
            $('#loginStep1').removeClass('d-none');
            $('#loginStep2').addClass('d-none');
            $('#loginStep3').addClass('d-none');
            $('#loginErrorBox').text('');
            $('#otpInput').val('');
        }

        function showStep2(maskedContact) {
            $('#loginStep1').addClass('d-none');
            $('#loginStep2').removeClass('d-none');
            $('#loginStep3').addClass('d-none');
            $('#maskedContactDisplay').text(maskedContact);
            startResendTimer();
        }

        function showStep3(verifiedMethodParam) {
            $('#loginStep1').addClass('d-none');
            $('#loginStep2').addClass('d-none');
            $('#loginStep3').removeClass('d-none');
            $('#signupErrorBox').text('');

            // Jo method verify hua, uska field mat maango — dusra maango
            if (verifiedMethodParam === 'email') {
                $('#signupPhoneWrap').removeClass('d-none');
                $('#signupEmailWrap').addClass('d-none');
            } else {
                $('#signupEmailWrap').removeClass('d-none');
                $('#signupPhoneWrap').addClass('d-none');
            }
        }

        function startResendTimer() {
            let seconds = 30;
            $('#resendCountdown').text(seconds);
            $('#resendTimerText').removeClass('d-none');
            $('#resendOtpLink').addClass('d-none');

            clearInterval(resendInterval);
            resendInterval = setInterval(function () {
                seconds--;
                $('#resendCountdown').text(seconds);
                if (seconds <= 0) {
                    clearInterval(resendInterval);
                    $('#resendTimerText').addClass('d-none');
                    $('#resendOtpLink').removeClass('d-none');
                }
            }, 1000);
        }

        $('#toggleMethodLink').on('click', function () {
            if (currentMethod === 'email') {
                currentMethod = 'mobile';
                $('#loginEmailInput').closest('.input-group').addClass('d-none');
                $('#loginPhoneInput').closest('.input-group').removeClass('d-none');
                $(this).text('With Email');
                $('#loginPromptText').text('Please enter your mobile number');
            } else {
                currentMethod = 'email';
                $('#loginPhoneInput').closest('.input-group').addClass('d-none');
                $('#loginEmailInput').closest('.input-group').removeClass('d-none');
                $(this).text('With Mobile');
                $('#loginPromptText').text('Please enter your email address');
            }
            $('#loginErrorBox').text('');
        });

        function sendOtp() {
            const payload = { method: currentMethod };

            if (currentMethod === 'email') {
                const email = $('#loginEmailInput').val().trim();
                if (!email) { $('#loginErrorBox').text('Please enter your email address.'); return; }
                payload.email = email;
            } else {
                const phone = $('#loginPhoneInput').val().trim();
                if (phone.length !== 10) { $('#loginErrorBox').text('Please enter a valid 10 digit mobile number.'); return; }
                payload.phone = phone;
            }

            $('#getOtpBtn').prop('disabled', true).text('Sending...');

            $.ajax({
                url: "{{ route('auth.send-otp') }}",
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                contentType: 'application/json',
                data: JSON.stringify(payload),
                success: function (data) {
                    $('#getOtpBtn').prop('disabled', false).html('Get OTP <i class="fa-solid fa-arrow-right ms-1"></i>');
                    if (data.success) {
                        showStep2(data.masked_contact);
                        if (data.debug_otp) {
                            console.log('%c[TEST MODE] Your OTP is: ' + data.debug_otp, 'color:green;font-weight:bold;font-size:14px;');
                            $('#otpInput').val(data.debug_otp);
                        }
                    } else {
                        $('#loginErrorBox').text(data.message);
                    }
                },
                error: function (xhr) {
                    $('#getOtpBtn').prop('disabled', false).html('Get OTP <i class="fa-solid fa-arrow-right ms-1"></i>');
                    $('#loginErrorBox').text(xhr.responseJSON?.message || 'Something went wrong.');
                }
            });
        }

        $('#getOtpBtn').on('click', sendOtp);
        $('#resendOtpLink').on('click', sendOtp);
        $('#editContactLink').on('click', showStep1);

        $('#verifyOtpBtn').on('click', function () {
            const otp = $('#otpInput').val().trim();
            if (otp.length !== 6) { $('#otpErrorBox').text('Please enter the 6 digit OTP.'); return; }

            const payload = { method: currentMethod, otp: otp };
            if (currentMethod === 'email') {
                payload.email = $('#loginEmailInput').val().trim();
            } else {
                payload.phone = $('#loginPhoneInput').val().trim();
            }

            $('#verifyOtpBtn').prop('disabled', true).text('Verifying...');

            $.ajax({
                url: "{{ route('auth.verify-otp') }}",
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                contentType: 'application/json',
                data: JSON.stringify(payload),
                success: function (data) {
                    $('#verifyOtpBtn').prop('disabled', false).html('Verify OTP <i class="fa-solid fa-arrow-right ms-1"></i>');

                    if (!data.success) {
                        $('#otpErrorBox').text(data.message);
                        return;
                    }

                    if (data.needs_signup) {
                        // Naya user — profile complete karwana hai
                        pendingUserId = data.user_id;
                        verifiedMethod = data.verified_method;
                        showStep3(verifiedMethod);
                    } else {
                        // Purana user — login ho gaya, page reload
                        location.reload();
                    }
                },
                error: function (xhr) {
                    $('#verifyOtpBtn').prop('disabled', false).html('Verify OTP <i class="fa-solid fa-arrow-right ms-1"></i>');
                    $('#otpErrorBox').text(xhr.responseJSON?.message || 'Something went wrong.');
                }
            });
        });

        $('#completeSignupBtn').on('click', function () {
            const name = $('#signupNameInput').val().trim();
            const password = $('#signupPasswordInput').val();
            const confirmPassword = $('#signupConfirmPasswordInput').val();

            if (!name) { $('#signupErrorBox').text('Please enter your full name.'); return; }
            if (password.length < 6) { $('#signupErrorBox').text('Password must be at least 6 characters.'); return; }
            if (password !== confirmPassword) { $('#signupErrorBox').text('Passwords do not match.'); return; }

            const payload = {
                user_id: pendingUserId,
                name: name,
                password: password,
                password_confirmation: confirmPassword,
            };

            if (verifiedMethod === 'email') {
                const phone = $('#signupPhoneInput').val().trim();
                if (phone.length !== 10) { $('#signupErrorBox').text('Please enter a valid 10 digit mobile number.'); return; }
                payload.phone = phone;
            } else {
                const email = $('#signupEmailInput').val().trim();
                if (!email) { $('#signupErrorBox').text('Please enter a valid email address.'); return; }
                payload.email = email;
            }

            $('#completeSignupBtn').prop('disabled', true).text('Creating Account...');

            $.ajax({
                url: "{{ route('auth.complete-signup') }}",
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                contentType: 'application/json',
                data: JSON.stringify(payload),
                success: function (data) {
                    if (data.success) {
                        location.reload();
                    } else {
                        $('#completeSignupBtn').prop('disabled', false).html('Create Account <i class="fa-solid fa-arrow-right ms-1"></i>');
                        $('#signupErrorBox').text(data.message);
                    }
                },
                error: function (xhr) {
                    $('#completeSignupBtn').prop('disabled', false).html('Create Account <i class="fa-solid fa-arrow-right ms-1"></i>');
                    $('#signupErrorBox').text(xhr.responseJSON?.message || 'Something went wrong.');
                }
            });
        });

        $('#showSignupLink').on('click', function () {
            // "New here? Create an Account" — same OTP flow use karta hai (email/mobile daal ke)
            // kyunki hamara sendOtp() already naye user ko auto-create kar deta hai (firstOrCreate)
            $('#loginPromptText').text('Enter your email or mobile to create an account');
            $('#loginEmailInput').val('').focus();
        });

        $('#loginModal').on('show.bs.modal', showStep1);
    });
</script>
@endpush
</parameter>
