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
                <form id="productForm" action="{{ route('pos.products.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf


                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold">Title <span
                                class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label fw-semibold">Category <span
                                class="text-danger">*</span></label>
                        <select name="category_id" id="category" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label fw-semibold">Price <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="price" id="price" step="0.01" class="form-control"
                                required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="compare_price" class="form-label fw-semibold">Compare Price</label>
                            <input type="number" name="compare_price" id="compare_price" step="0.01"
                                class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label fw-semibold">Product Image</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sku" class="form-label fw-semibold">SKU</label>
                            <input type="text" name="sku" id="sku" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="weight" class="form-label fw-semibold">Weight</label>
                            <input type="number" name="weight" id="weight" step="0.01" class="form-control">
                        </div>
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
@section('scripts')
    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                }
            });

            $('#productForm').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#message').html('<div class="alert alert-info">Saving...</div>');
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#message').html('<div class="alert alert-success">' + response
                                .message + '</div>');
                            $('#productForm')[0].reset();
                            window.location.href = "{{ route('pos.products.index') }}";
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let html = '<div class="alert alert-danger"><ul>';
                            $.each(errors, function(key, value) {
                                html += '<li>' + value[0] + '</li>';
                            });
                            html += '</ul> </div>';
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
@endsection
