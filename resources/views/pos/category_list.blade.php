@extends('pos.layout.layout')

@section('content')
    <div class="container mt-5">
        <div class="card shadow-sm rounded-4 border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-folder2-open me-2"></i>Categories List</h5>
                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="bi bi-plus-circle me-1"></i> <b>Add Category</b>
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 20%;">Name</th>
                                <th style="width: 15%;">Parent</th>
                                <th style="width: 15%;">Image</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 20%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr id="row-{{ $category->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-start fw-semibold">{{ $category->name }}</td>
                                    <td class="text-start">{{ $category->parent->name ?? '-' }}</td>
                                    <td>
                                        @if ($category->image)
                                            <img src="{{ asset('storage/' . $category->image) }}"
                                                class="img-thumbnail rounded shadow-sm"
                                                style="width: 55px; height: 55px; object-fit: cover;">
                                        @else
                                            <span class="text-muted fst-italic">No Image</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span id="badge-{{ $category->id }}"
                                            class="badge px-3 py-2 {{ $category->status ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $category->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Edit -->
                                            <button type="button" class="btn btn-warning btn-sm edit-btn"
                                                data-id="{{ $category->id }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <!-- Delete -->
                                            <button type="button" class="btn btn-danger btn-sm delete-btn"
                                                data-id="{{ $category->id }}"
                                                data-url="{{ route('categories.destroy', $category->id) }}">
                                                <i class="bi bi-trash"></i>
                                            </button>

                                            <!-- Toggle -->
                                            <button type="button"
                                                class="btn btn-sm toggle-status-btn {{ $category->status ? 'btn-success' : 'btn-secondary' }}"
                                                data-id="{{ $category->id }}">
                                                {{ $category->status ? 'Active' : 'Inactive' }}
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $categories->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Add Category Modal --}}
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addCategoryForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Parent</label>
                            <select name="parent_id" class="form-select">
                                <option value="">None</option>
                                @foreach ($categories as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Edit Category Modal --}}
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editCategoryForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="edit_category_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" id="edit_name" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Parent</label>
                            <select id="edit_parent_id" name="parent_id" class="form-select">
                                <option value="">None</option>
                                @foreach ($categories as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" id="image" name="image" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select id="edit_status" name="status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle me-2"></i> Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete this category?</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteCategoryForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            // add category
            $('#addCategoryForm').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let formData = new FormData(this);

                $.ajax({
                    url: '{{ route('categories.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.redirect) {
                            $('#addCategoryModal').modal('hide');
                            window.location.href = data.redirect;
                        } else {
                            alert('Error: ' + (data.message || 'Something went wrong.'));
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = xhr.responseJSON?.message || 'An error occurred!';
                        alert('Error: ' + errorMsg);
                    }
                });
            });


            // Edit Category 
            $(document).on('click', '.edit-btn', function() {
                let categoryId = $(this).data('id');

                $.get("{{ route('category.show', ':id') }}".replace(':id', categoryId), function(res) {
                    let category = res.category;

                    $('#edit_category_id').val(category.id);
                    $('#edit_name').val(category.name);
                    $('#edit_parent_id').val(category.parent_id || '');
                    $('#edit_status').val(category.status ? 1 : 0);

                    $('#editCategoryForm').attr(
                        'action',
                        "{{ route('categories.update', ':id') }}".replace(':id', categoryId)
                    );

                    // Show modal
                    $('#editCategoryModal').modal('show');
                });
            });

            // Delete Category
            $(document).on('click', '.delete-btn', function() {
                let categoryId = $(this).data('id');
                let deleteUrl = $(this).data('url');

                // Update form action dynamically
                $('#deleteCategoryForm').attr('action', deleteUrl);

                // Show modal
                $('#deleteConfirmModal').modal('show');
            });

            // Toggle Status
            $(document).on('click', '.toggle-status-btn', function() {
                let btn = $(this);
                let id = btn.data('id');

                $.ajax({
                    url: "{{ route('category.toggleStatus', ':id') }}".replace(':id', id),
                    type: "PATCH",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status == 1) {
                            btn.removeClass('btn-secondary').addClass('btn-success').html(
                                'Active');
                            $('#badge-' + id).removeClass('bg-secondary').addClass('bg-success')
                                .text('Active');
                        } else {
                            btn.removeClass('btn-success').addClass('btn-secondary').html(
                                'Inactive');
                            $('#badge-' + id).removeClass('bg-success').addClass('bg-secondary')
                                .text('Inactive');
                        }
                    },
                    error: function() {
                        alert("Something went wrong!");
                    }
                });
            });

        });
    </script>
@endsection
