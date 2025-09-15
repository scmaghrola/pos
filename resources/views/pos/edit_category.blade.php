@extends('pos.layout.admin')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm rounded-3">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Category</h5>
            <a href="{{ route('category.list') }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-left-circle me-1"></i> Back to Categories
            </a>
        </div>
        <div class="card-body">
            <form id="editCategoryForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Category Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $category->name }}" required>
                </div>

                <!-- Parent Category -->
                <div class="mb-3">
                    <label for="parent_id" class="form-label">Parent Category</label>
                    <select name="parent_id" id="parent_id" class="form-select">
                        <option value="">-- Select Parent Category --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $category->parent_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Image -->
                <div class="mb-3">
                    <label for="image" class="form-label">Category Image</label>
                    @if ($category->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $category->image) }}" alt="Category Image" width="100">
                        </div>
                    @endif
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                </div>

                <!-- Submit Button -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check-circle me-1"></i> Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#editCategoryForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append('_method', 'PUT'); // Important for Laravel

        $.ajax({
            url: '{{ route("categories.update", $category->id) }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                alert(response.message);
                window.location.href = '{{ route("category.list") }}';
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON.message);
            }
        });
    });
});
</script>
@endsection
