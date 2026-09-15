@extends('admin.layouts.app')

@section('title', 'Categories')

@section('content')
<style>
.category-admin-page{max-width:1500px;margin:0 auto}.category-page-head{padding:25px 28px;margin-bottom:22px;border:1px solid #eadfd9;border-radius:18px;background:linear-gradient(120deg,#fff,#fff7f2);box-shadow:0 9px 28px rgba(74,46,43,.07)}.category-page-head h4{font-size:1.65rem;font-weight:700;color:#35221f}.category-page-head small{display:block;margin-top:5px;font-size:.82rem}.category-head-actions{display:flex;flex-wrap:wrap;gap:9px}.category-head-actions .btn{padding:10px 15px;border-radius:9px;font-size:.82rem;font-weight:600}.category-head-actions .btn-dark{border-color:#ff1730;background:#ff1730;box-shadow:0 6px 16px rgba(232,23,45,.17)}.category-head-actions .btn-dark:hover{border-color:#dc1026;background:#dc1026}.category-head-actions .btn-outline-dark{border-color:#e2c8c1;color:#513a35;background:#fff}.category-head-actions .btn-outline-dark:hover{border-color:#ff1730;background:#fff0ec;color:#e8172d}
.category-table-card{overflow:hidden;border:1px solid #eadfd9!important;border-radius:18px!important;box-shadow:0 10px 30px rgba(74,46,43,.07)!important}.category-table-card .card-body{padding:22px}.category-table-card table.dataTable{margin-top:14px!important}.category-table-card thead th{padding:15px 12px!important;border-bottom:1px solid #eaded9!important;background:#fff8f5!important;color:#4a302c;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px}.category-table-card tbody td{padding:11px 12px!important;border-color:#f0e7e3!important;color:#463431;vertical-align:middle}.category-table-card tbody tr{transition:background .15s ease}.category-table-card tbody tr:hover{background:#fffaf8}.category-table-card tbody img{width:54px!important;height:54px!important;border:3px solid #fff;border-radius:11px!important;object-fit:cover;box-shadow:0 4px 12px rgba(68,35,28,.14)}.category-table-card tbody td:nth-child(2){font-weight:600}.category-table-card .badge{padding:6px 10px!important;border-radius:7px!important;font-size:.67rem!important}.category-table-card .btn{padding:6px 10px;border-radius:7px;font-size:.75rem}.category-table-card .btn-outline-primary{border-color:#d9c5bf;color:#5c4540}.category-table-card .btn-outline-primary:hover{border-color:#ff1730;background:#ff1730;color:#fff}.category-table-card .btn-outline-danger{border-color:#f4c3c8;color:#e8172d}.category-table-card .btn-outline-danger:hover{background:#e8172d;color:#fff}.category-table-card .dataTables_filter input,.category-table-card .dataTables_length select{border:1px solid #dfd2cc;border-radius:8px!important;box-shadow:none}.category-table-card .dataTables_filter input:focus{border-color:#ff8290;box-shadow:0 0 0 3px rgba(255,23,48,.1)}.category-table-card .page-link{color:#4a2e2b}.category-table-card .page-item.active .page-link{border-color:#ff1730;background:#ff1730;color:#fff}
.category-modal .modal-content{overflow:hidden;border:0;border-radius:18px;box-shadow:0 24px 65px rgba(46,27,24,.2)}.category-modal .modal-header{padding:20px 24px;border-color:#eee3de;background:#fff8f4}.category-modal .modal-title{font-weight:700;color:#3a2623}.category-modal .modal-body{padding:24px}.category-modal .form-label{font-size:.8rem;font-weight:600;color:#4d3b37}.category-modal .form-control,.category-modal .form-select{min-height:45px;border-color:#ded2cd;border-radius:9px}.category-modal .form-control:focus,.category-modal .form-select:focus{border-color:#ff7787;box-shadow:0 0 0 3px rgba(255,23,48,.1)}.category-modal .modal-footer{padding:16px 24px;border-color:#eee3de;background:#fffaf8}.category-modal .modal-footer .btn-dark{border-color:#ff1730;background:#ff1730}
@media(max-width:850px){.category-page-head{padding:20px;align-items:flex-start!important;gap:16px;flex-direction:column}.category-table-card .card-body{padding:14px;overflow-x:auto}.category-head-actions{width:100%}.category-head-actions .btn{flex:1}}
</style>
<div class="category-admin-page">
<div class="category-page-head d-flex justify-content-between align-items-center">
    <div>
        <h4 class="mb-0">Category Management</h4>
        <small class="text-muted">Manage Categories, Subcategories, and Child Categories</small>
    </div>
    <div class="category-head-actions">
        <button type="button" class="btn btn-dark" onclick="openAddModal('category')"><i class="fa-solid fa-plus me-1"></i> Add Category</button>
        <button type="button" class="btn btn-outline-dark" onclick="openAddModal('subcategory')"><i class="fa-solid fa-plus me-1"></i> Add Subcategory</button>
        <button type="button" class="btn btn-outline-dark" onclick="openAddModal('child')"><i class="fa-solid fa-plus me-1"></i> Add Child Category</button>
    </div>
</div>

<div class="card category-table-card">
    <div class="card-body">
        <table id="categoryTable" class="table table-hover align-middle w-100">
            <thead class="table-light">
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Level</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Add / Edit Modal -->
<div class="modal fade category-modal" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="categoryForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="level" id="level" value="category">

                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3" id="categoryFieldWrap" style="display:none;">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select">
                                <option value="">-- Select Category --</option>
                                @foreach ($topLevelCategories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="subcategoryFieldWrap" style="display:none;">
                            <label class="form-label">Subcategory <span class="text-danger">*</span></label>
                            <select name="subcategory_id" id="subcategory_id" class="form-select">
                                <option value="">-- Select Category First --</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" id="nameLabel">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control">
                            <img id="imagePreview" class="mt-2 rounded d-none" width="60">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" id="sort_order" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" checked>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
    const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
    const categoryForm = document.getElementById('categoryForm');
    const categorySelect = document.getElementById('category_id');
    const subcategorySelect = document.getElementById('subcategory_id');
    const categoryFieldWrap = document.getElementById('categoryFieldWrap');
    const subcategoryFieldWrap = document.getElementById('subcategoryFieldWrap');
    const levelInput = document.getElementById('level');
    const nameLabel = document.getElementById('nameLabel');
    const modalTitle = document.getElementById('categoryModalLabel');

    function resetSelect(select, placeholder) {
        select.innerHTML = `<option value="">${placeholder}</option>`;
    }

    function loadChildren(parentId, targetSelect, selectedId = null) {
        resetSelect(targetSelect, '-- Select --');
        if (!parentId) return Promise.resolve();

        return fetch(`/admin/categories/${parentId}/children`)
            .then(res => res.json())
            .then(items => {
                items.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name;
                    if (selectedId && item.id == selectedId) option.selected = true;
                    targetSelect.appendChild(option);
                });
            });
    }

    categorySelect.addEventListener('change', function () {
        loadChildren(this.value, subcategorySelect);
    });

    /**
     * type: 'category' | 'subcategory' | 'child'
     * Shows only the relevant parent dropdowns and updates labels/required attributes.
     */
    function configureModalForLevel(type) {
        levelInput.value = type;
        categorySelect.required = false;
        subcategorySelect.required = false;
        categoryFieldWrap.style.display = 'none';
        subcategoryFieldWrap.style.display = 'none';

        if (type === 'category') {
            modalTitle.textContent = 'Add Category';
            nameLabel.textContent = 'Category Name *';
        }

        if (type === 'subcategory') {
            modalTitle.textContent = 'Add Subcategory';
            nameLabel.textContent = 'Subcategory Name *';
            categoryFieldWrap.style.display = 'block';
            categorySelect.required = true;
        }

        if (type === 'child') {
            modalTitle.textContent = 'Add Child Category';
            nameLabel.textContent = 'Child Category Name *';
            categoryFieldWrap.style.display = 'block';
            subcategoryFieldWrap.style.display = 'block';
            categorySelect.required = true;
            subcategorySelect.required = true;
        }
    }

    function openAddModal(type) {
        categoryForm.reset();
        categoryForm.action = "{{ route('admin.categories.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('imagePreview').classList.add('d-none');
        resetSelect(subcategorySelect, '-- Select Category First --');
        configureModalForLevel(type);
        categoryModal.show();
    }

    function openEditModal(categoryId) {
        fetch(`/admin/categories/${categoryId}/fetch`)
            .then(res => res.json())
            .then(async data => {
                categoryForm.action = `/admin/categories/${categoryId}`;
                document.getElementById('formMethod').value = 'PUT';

                const type = data.depth === 0 ? 'category' : (data.depth === 1 ? 'subcategory' : 'child');
                configureModalForLevel(type);
                modalTitle.textContent = 'Edit ' + (type === 'category' ? 'Category' : type === 'subcategory' ? 'Subcategory' : 'Child Category');

                document.getElementById('name').value = data.name;
                document.getElementById('description').value = data.description ?? '';
                document.getElementById('sort_order').value = data.sort_order;
                document.getElementById('is_active').checked = data.is_active;

                const preview = document.getElementById('imagePreview');
                if (data.image_url) {
                    preview.src = data.image_url;
                    preview.classList.remove('d-none');
                } else {
                    preview.classList.add('d-none');
                }

                if (data.category_id) {
                    categorySelect.value = data.category_id;
                }

                if (type === 'child' && data.category_id) {
                    await loadChildren(data.category_id, subcategorySelect, data.subcategory_id);
                }

                categoryModal.show();
            });
    }

    function confirmDelete(categoryId) {
        if (confirm('Are you sure you want to delete this?')) {
            document.getElementById(`deleteForm${categoryId}`).submit();
        }
    }

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('status-toggle')) {
            fetch(`/admin/categories/${e.target.dataset.id}/toggle-status`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            });
        }
    });

    $(function () {
        $('#categoryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.categories.data') }}",
            columns: [
                { data: 'image', name: 'image', orderable: false, searchable: false },
                { data: 'display_name', name: 'name' },
                { data: 'level', name: 'level', orderable: false, searchable: false },
                { data: 'products_count', name: 'products_count', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
            ],
        });
    });
</script>
@endpush
