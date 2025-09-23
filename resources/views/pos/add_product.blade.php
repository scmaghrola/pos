@extends('pos.layout.admin')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i> Add Product</h5>
                <a href="{{ route('pos.products.index') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> <b>Back to List</b>
                </a>
            </div>
            <div class="card-body">

                <div id="message"></div>

                <form id="productForm" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Price <span class="text-danger">*</span></label>
                        <input type="number" name="price" step="0.01" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product Image</label>
                        <div class="dropzone" id="myDropzone"></div>
                        <input type="hidden" name="image" id="productImage">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Save Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        Dropzone.autoDiscover = false;

        let myDropzone = new Dropzone("#myDropzone", {
            url: "{{ route('pos.products.upload-image') }}", // updated route
            paramName: "image",
            maxFiles: 1,
            maxFilesize: 5,
            acceptedFiles: "image/*",
            addRemoveLinks: true,
            dictDefaultMessage: "Drag & drop an image or click here",
            headers: { // <-- add CSRF token here
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(file, response) {
                $('#productImage').val(response.filename); // store uploaded filename
            },
            removedfile: function(file) {
                $('#productImage').val(''); // clear hidden input
                file.previewElement.remove();
            }
        });

        $(document).ready(function() {
            $('#productForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('pos.products.store') }}", // updated route
                    type: "POST",
                    data: formData,
                    beforeSend: function() {
                        $('#message').html('<div class="alert alert-info">Saving...</div>');
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#message').html('<div class="alert alert-success">' + response
                                .message + '</div>');
                            setTimeout(() => {
                                // window.location.href ="{{ route('pos.products.index') }}";
                            }, 1000);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let html = '<div class="alert alert-danger"><ul>';
                            $.each(errors, function(key, value) {
                                html += '<li>' + value[0] + '</li>';
                            });
                            html += '</ul></div>';
                            $('#message').html(html);
                        } else {
                            $('#message').html(
                                '<div class="alert alert-danger">Something went wrong!</div>'
                                );
                        }
                    }
                });
            });
        });
    </script>
@endpush
