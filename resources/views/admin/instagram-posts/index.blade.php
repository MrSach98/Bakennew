@extends('admin.layouts.app')

@section('title', 'Instagram Posts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Instagram Posts</h4>
        <small class="text-muted">Ye homepage ke "What's In Your Heart?" section me dikhenge</small>
    </div>
    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#igModal" onclick="openAddModal()">
        + Add Instagram Post
    </button>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <table id="igTable" class="table table-hover align-middle w-100">
            <thead class="table-light">
                <tr>
                    <th>Image</th>
                    <th>Post Link</th>
                    <th>Caption</th>
                    <th>Sort Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="igModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="igForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title" id="igModalLabel">Add Instagram Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Instagram Post/Reel Link <span class="text-danger">*</span></label>
                        <input type="url" name="post_url" id="post_url" class="form-control" placeholder="https://www.instagram.com/reel/xxxxxxxxx/" required>
                        <div class="form-text">Isi link par customer "View" click karte hi pahunchega.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Image/Thumbnail <span class="text-danger" id="imageRequiredMark">*</span></label>
                        <input type="file" name="thumbnail_image" class="form-control" accept="image/*">
                        <img id="imagePreview" class="mt-2 rounded d-none" width="80">
                        <div class="form-text">Video ke liye uska ek screenshot/thumbnail yahan upload karo — ye hi site par dikhega.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Caption (optional)</label>
                        <input type="text" name="caption" id="caption" class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" id="sort_order" class="form-control" value="0">
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" checked>
                                <label class="form-check-label">Active</label>
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
@endsection

@push('scripts')
<script>
    const igModal = new bootstrap.Modal(document.getElementById('igModal'));
    const igForm = document.getElementById('igForm');

    function openAddModal() {
        document.getElementById('igModalLabel').textContent = 'Add Instagram Post';
        igForm.action = "{{ route('admin.instagram-posts.store') }}";
        document.getElementById('formMethod').value = 'POST';
        igForm.reset();
        document.getElementById('imagePreview').classList.add('d-none');
        document.getElementById('imageRequiredMark').style.display = 'inline';
        igForm.querySelector('input[name="thumbnail_image"]').required = true;
    }

    function openEditModal(id) {
        fetch(`/admin/instagram-posts/${id}/fetch`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('igModalLabel').textContent = 'Edit Instagram Post';
                igForm.action = `/admin/instagram-posts/${id}`;
                document.getElementById('formMethod').value = 'PUT';

                document.getElementById('post_url').value = data.post_url;
                document.getElementById('caption').value = data.caption ?? '';
                document.getElementById('sort_order').value = data.sort_order;
                document.getElementById('is_active').checked = data.is_active;

                const preview = document.getElementById('imagePreview');
                preview.src = data.thumbnail_url;
                preview.classList.remove('d-none');

                document.getElementById('imageRequiredMark').style.display = 'none';
                igForm.querySelector('input[name="thumbnail_image"]').required = false;

                igModal.show();
            });
    }

    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this post?')) {
            document.getElementById(`deleteForm${id}`).submit();
        }
    }

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('status-toggle')) {
            fetch(`/admin/instagram-posts/${e.target.dataset.id}/toggle-status`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            });
        }
    });

    $(function () {
        $('#igTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.instagram-posts.data') }}",
            columns: [
                { data: 'thumbnail', name: 'thumbnail', orderable: false, searchable: false },
                { data: 'link', name: 'post_url' },
                { data: 'caption', name: 'caption' },
                { data: 'sort_order', name: 'sort_order' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
            ],
        });
    });
</script>
@endpush