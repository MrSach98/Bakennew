@include('userheader')

<style>
    .star-input span { cursor: pointer; font-size: 1.8rem; color: #ccc; }
    .star-input span.active { color: #ffb800; }
</style>

<div class="container-fluid px-4 px-lg-5 py-4">
    <h1 class="mb-4" style="font-size:1.6rem;">My Account</h1>

    <div class="row">
        @include('account.partials.sidebar')

        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">My Reviews</h5>

                    <ul class="nav nav-pills mb-3">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tabToReview" type="button">
                                To Review
                                @if ($pendingItems->count())
                                    <span class="badge bg-danger ms-1">{{ $pendingItems->count() }}</span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabMyReviews" type="button">
                                My Reviews ({{ $reviews->total() }})
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- To Review -->
                        <div class="tab-pane fade show active" id="tabToReview">
                            @forelse ($pendingItems as $item)
                                @php $img = $item->product->primaryImage ?? $item->product->images->first(); @endphp
                                <div class="d-flex align-items-center gap-3 border rounded-3 p-3 mb-3">
                                    <img src="{{ $img ? asset($img->image_path) : 'https://placehold.co/70x70/FFF8F0/D8232A?text=Cake' }}"
                                         width="70" height="70" class="rounded" style="object-fit:cover;">
                                    <div class="flex-grow-1">
                                        <a href="{{ route('product.show', $item->product->slug) }}" class="fw-semibold text-decoration-none text-dark">{{ $item->product_name }}</a>
                                        <div class="small text-muted">{{ $item->weight_label }} &middot; Order #{{ $item->order->order_number }}</div>
                                        <div class="small text-muted">Delivered on {{ $item->order->delivery_date?->format('d M Y') }}</div>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm write-review-btn"
                                            data-order-item-id="{{ $item->id }}"
                                            data-product-id="{{ $item->product_id }}"
                                            data-product-name="{{ $item->product_name }}">
                                        Write a Review
                                    </button>
                                </div>
                            @empty
                                <p class="text-muted text-center py-4 mb-0">Nothing to review right now. Products from your delivered orders will show up here.</p>
                            @endforelse
                        </div>

                        <!-- My Reviews -->
                        <div class="tab-pane fade" id="tabMyReviews">
                            @forelse ($reviews as $review)
                                <div class="border rounded-3 p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-bold">{{ $review->product->name ?? 'Product' }}</div>
                                            <div style="color:#ffb800;">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
                                        </div>
                                        <span class="badge bg-{{ ['pending' => 'warning text-dark', 'approved' => 'success', 'rejected' => 'danger'][$review->status] }}">
                                            {{ ucfirst($review->status) }}
                                        </span>
                                    </div>
                                    @if ($review->title)<div class="fw-semibold mt-2">{{ $review->title }}</div>@endif
                                    @if ($review->comment)<p class="small text-muted mb-2">{{ $review->comment }}</p>@endif
                                    @if ($review->images->count())
                                        <div class="d-flex gap-2">
                                            @foreach ($review->images as $img)
                                                <img src="{{ asset($img->image_path) }}" width="50" height="50" class="rounded" style="object-fit:cover;">
                                            @endforeach
                                        </div>
                                    @endif
                                    <small class="text-muted d-block mt-2">{{ $review->created_at->format('d M Y') }}</small>
                                </div>
                            @empty
                                <p class="text-muted text-center py-4 mb-0">You haven't written any reviews yet.</p>
                            @endforelse

                            <div class="mt-3">{{ $reviews->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review: <span id="reviewProductName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reviewOrderItemId">
                <input type="hidden" id="reviewProductId">
                <input type="hidden" id="reviewRating" value="0">

                <div class="mb-3">
                    <label class="form-label small">Your Rating</label>
                    <div class="star-input" id="starInput">
                        <span data-value="1">★</span><span data-value="2">★</span><span data-value="3">★</span><span data-value="4">★</span><span data-value="5">★</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Title (optional)</label>
                    <input type="text" id="reviewTitle" class="form-control" maxlength="150">
                </div>
                <div class="mb-3">
                    <label class="form-label small">Your Review (optional)</label>
                    <textarea id="reviewComment" class="form-control" rows="3" maxlength="1000"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Add Photos (optional, up to 5)</label>
                    <input type="file" id="reviewImages" class="form-control" accept="image/*" multiple>
                    <div id="reviewImagePreview" class="d-flex gap-2 mt-2 flex-wrap"></div>
                </div>
                <div id="reviewErrorBox" class="text-danger small"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="submitReviewBtn">Submit Review</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    const reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));

    $('.write-review-btn').on('click', function () {
        $('#reviewOrderItemId').val($(this).data('order-item-id'));
        $('#reviewProductId').val($(this).data('product-id'));
        $('#reviewProductName').text($(this).data('product-name'));
        $('#reviewRating').val(0);
        $('#starInput span').removeClass('active');
        $('#reviewTitle, #reviewComment').val('');
        $('#reviewImages').val('');
        $('#reviewImagePreview').empty();
        $('#reviewErrorBox').text('');
        reviewModal.show();
    });

    $('#starInput span').on('click', function () {
        const value = parseInt($(this).data('value'), 10);
        $('#reviewRating').val(value);
        $('#starInput span').each(function () {
            $(this).toggleClass('active', parseInt($(this).data('value'), 10) <= value);
        });
    });

    $('#reviewImages').on('change', function () {
        const $preview = $('#reviewImagePreview').empty();
        Array.from(this.files).slice(0, 5).forEach(file => {
            $preview.append($('<img>', { src: URL.createObjectURL(file), width: 60, height: 60, css: { objectFit: 'cover', borderRadius: '6px' } }));
        });
    });

    $('#submitReviewBtn').on('click', function () {
        if ($('#reviewRating').val() === '0') {
            $('#reviewErrorBox').text('Please select a star rating.');
            return;
        }

        const formData = new FormData();
        formData.append('product_id', $('#reviewProductId').val());
        formData.append('order_item_id', $('#reviewOrderItemId').val());
        formData.append('rating', $('#reviewRating').val());
        formData.append('title', $('#reviewTitle').val());
        formData.append('comment', $('#reviewComment').val());
        Array.from($('#reviewImages')[0].files).slice(0, 5).forEach(f => formData.append('images[]', f));

        const $btn = $(this).prop('disabled', true).text('Submitting...');

        $.ajax({
            url: "{{ route('reviews.store') }}",
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                showToast(data.message, 'success');
                reviewModal.hide();
                setTimeout(() => location.reload(), 1200);
            },
            error: function (xhr) {
                $btn.prop('disabled', false).text('Submit Review');
                $('#reviewErrorBox').text(xhr.responseJSON?.message || 'Something went wrong.');
            }
        });
    });
});
</script>
@endpush

@include('userfooter')