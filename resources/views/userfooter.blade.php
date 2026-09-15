<section class="footer-about-section" aria-labelledby="footer-about-title">
    <div class="footer-about-inner">
        <div class="footer-about-intro">
            <span class="footer-about-kicker">Why choose Bakeon</span>
            <h2 id="footer-about-title">Celebrate Every Occasion with Bakeon</h2>
            <p>Thoughtfully crafted cakes and desserts, made fresh for birthdays, anniversaries and every special moment.</p>
        </div>

        <div class="footer-about-content" id="footerAboutContent">
            <h3>Bakeon: Fresh Cakes Delivered with Love</h3>
            <p>Bakeon makes celebrations sweeter with freshly baked cakes, creative designs and dependable delivery. Explore birthday cakes, anniversary cakes, designer creations, bento cakes, cookies and desserts for every taste and occasion. Each order is thoughtfully prepared using quality ingredients, carefully packed and delivered to help you celebrate without worry.</p>
            <div class="footer-about-more" id="footerAboutMore">
                <p>Choose from classic flavours, personalised themes and special collections created for the people you love. Whether it is a small surprise or a grand celebration, Bakeon helps you find the perfect centrepiece and delivers it with the care your moment deserves.</p>
            </div>
            <button class="footer-read-more" type="button" aria-expanded="false" aria-controls="footerAboutMore">Read More <i class="fa-solid fa-angle-down"></i></button>
        </div>

        <div class="footer-service-highlights">
            <div><i class="fa-solid fa-truck-fast"></i><span><strong>Reliable Delivery</strong><small>Carefully delivered at your selected time</small></span></div>
            <div><i class="fa-solid fa-shield-halved"></i><span><strong>100% Safe &amp; Secure Payments</strong><small>Pay using trusted payment methods</small></span></div>
            <div><i class="fa-solid fa-headset"></i><span><strong>Dedicated Help Center</strong><small><a href="{{ url('/contact-us') }}">Connect With Us</a></small></span></div>
        </div>
    </div>
</section>

<footer class="store-footer">
    <div class="footer-shell">
        <section class="footer-newsletter" aria-label="Newsletter subscription">
            <div class="footer-newsletter-icon"><i class="fa-regular fa-envelope"></i></div>
            <div class="footer-newsletter-copy">
                <span>Fresh from the oven</span>
                <strong>Sweet offers, delivered to your inbox</strong>
                <p>Be the first to know about new cakes, festive collections and special deals.</p>
            </div>
            <form class="footer-subscribe-form">
                <label class="visually-hidden" for="footer-email">Email address</label>
                <input id="footer-email" type="email" placeholder="Enter your email address">
                <button type="button">Subscribe <i class="fa-solid fa-arrow-right"></i></button>
            </form>
        </section>
        <div class="footer-main">
            <div class="footer-brand">
                <a class="footer-logo" href="{{ url('/') }}">
                    @if($siteSettings->logo ?? null)<img src="{{ asset($siteSettings->logo) }}" alt="{{ $siteSettings->store_name ?? 'Bakeon' }}">@else bakeon @endif
                </a>
                <p>Freshly baked happiness for birthdays, anniversaries and every little celebration in between.</p>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a><a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a><a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a><a href="#" aria-label="Pinterest"><i class="fa-brands fa-pinterest-p"></i></a>
                </div>
            </div>
            <nav class="footer-column" aria-label="Shop by category">
                <h3>Shop By Category</h3>
                @if(isset($footerCategories)) @foreach($footerCategories->take(6) as $category)<a href="{{ url('/'.$category->slug) }}">{{ $category->name }}</a>@endforeach @endif
            </nav>
            <nav class="footer-column" aria-label="Useful links">
                <h3>Useful Links</h3>
                <a href="{{ url('/about-us') }}">About Us</a><a href="{{ url('/contact-us') }}">Contact Us</a><a href="{{ url('/track-order') }}">Track Your Order</a><a href="{{ url('/privacy-policy') }}">Privacy Policy</a><a href="{{ url('/refund-policy') }}">Cancellation &amp; Refund</a>
            </nav>
            <div class="footer-column footer-contact">
                <h3>We're Here To Help</h3>
                <a href="mailto:{{ $siteSettings->contact_email ?? 'care@bakeon.com' }}"><i class="fa-regular fa-envelope"></i><span><small>Email us</small>{{ $siteSettings->contact_email ?? 'care@bakeon.com' }}</span></a>
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteSettings->contact_phone ?? '+918882202022') }}"><i class="fa-solid fa-phone"></i><span><small>Call us</small>{{ $siteSettings->contact_phone ?? '+91 88822 02022' }}</span></a>
                @if(!empty($siteSettings->address))<p><i class="fa-solid fa-location-dot"></i><span>{{ $siteSettings->address }}</span></p>@endif
                <div class="footer-contact-payments" aria-label="Accepted payment methods">
                    <small>We accept</small>
                    <img src="{{ asset('images/payment-methods.svg') }}?v=2" alt="Visa, Mastercard, Google Pay and UPI accepted">
                </div>
            </div>
        </div>
        <div class="footer-bottom"><span>&copy; {{ date('Y') }} {{ $siteSettings->store_name ?? 'Bakeon' }}. All rights reserved.</span><div><a href="{{ url('/terms-and-conditions') }}">Terms &amp; Conditions</a><a href="{{ url('/privacy-policy') }}">Privacy Policy</a></div></div>
    </div>
</footer>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 2000;">
    <div id="appToast" class="toast align-items-center text-white border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="appToastBody"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
function showToast(message, type = 'success') {
    const toastEl = document.getElementById('appToast');
    const toastBody = document.getElementById('appToastBody');
    toastEl.className = 'toast align-items-center text-white border-0 bg-' + (type === 'danger' ? 'danger' : 'success');
    toastBody.textContent = message;
    new bootstrap.Toast(toastEl, { delay: 4000 }).show();
}

@if (session('success'))
    document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));
@endif
@if (session('error'))
    document.addEventListener('DOMContentLoaded', () => showToast(@json(session('error')), 'danger'));
@endif
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const button = document.querySelector('.footer-read-more');
    const more = document.getElementById('footerAboutMore');
    if (!button || !more) return;
    button.addEventListener('click', function () {
        const open = more.classList.toggle('is-open');
        button.setAttribute('aria-expanded', open ? 'true' : 'false');
        button.innerHTML = open
            ? 'Read Less <i class="fa-solid fa-angle-up"></i>'
            : 'Read More <i class="fa-solid fa-angle-down"></i>';
    });
});
</script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
@stack('scripts')
</body>
</html>
