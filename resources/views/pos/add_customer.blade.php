@extends('pos.layout.admin')
@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm rounded-3">

            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Add Customer</h5>
                </div>
                <a href="{{ route('customer.list') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left me-1"></i><b> Back to List</b>
                </a>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('customer.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- First Name -->
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" id="first_name" class="form-control" required>
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" id="last_name" class="form-control" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <!-- Phone -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control" required>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-1"></i> Add Customer
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

            $('#customerForm').on('submit', function(e) {
                e.preventDefault(); // prevent default form submission

                let formData = $(this).serialize(); // serialize form data

                $.ajax({
                    url: "{{ route('customer.store') }}",
                    method: "POST",
                    data: formData,
                    success: function(response) {
                        // Show success message
                        $('#alert-placeholder').html(
                            '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            response.success +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>'
                        );
                        // Reset form
                        $('#customerForm')[0].reset();
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = '<div class="alert alert-danger"><ul>';
                        $.each(errors, function(key, value) {
                            errorHtml += '<li>' + value[0] + '</li>';
                        });
                        errorHtml += '</ul></div>';
                        $('#alert-placeholder').html(errorHtml);
                    }
                });
            });

        });
    </script>
@endsection
