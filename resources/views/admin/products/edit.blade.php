@extends('admin.layouts.app')
@section('title', 'Edit Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Product</h4>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Products
    </a>
</div>

{{-- ✅ Data prepare karo pehle, phir @json mein daalo --}}
@if(isset($product))
    @php
        $variantData = $product->weightVariants->map(function ($variant) {
            return [
                'weight_id' => $variant->weight_id,
                'egg_type' => $variant->egg_type,
                'price' => $variant->price,
                'discount_price' => $variant->discount_price,
                'stock' => $variant->stock,
                'is_default' => (bool) $variant->is_default,
            ];
        })->values();
    @endphp

    <script>
        window.existingVariants = @json($variantData);

        window.existingCategorySelection = {
            category_id: {{ $product->category_id ?? 'null' }},
            subcategory_id: {{ $product->subcategory_id ?? 'null' }},
            child_category_id: {{ $product->child_category_id ?? 'null' }}
        };
    </script>
@endif

<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" id="productForm">
    @method('PUT')
    @include('admin.products._form')
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/product-form.js') }}"></script>
@endpush