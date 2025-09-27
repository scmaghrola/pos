@extends('pos.layout.layout')

@section('content')
    <div class="container mt-5">
        <div class="card shadow-sm rounded-4 border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-people me-2"></i>Customers List</h5>

                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                    <i class="bi bi-plus-circle me-1"></i> <b>Add Customer</b>
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>#</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @forelse($customers as $customer)
                                <tr id="row-{{ $customer->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $customer->first_name }}</td>
                                    <td>{{ $customer->last_name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>
                                        <span id="badge-{{ $customer->id }}"
                                            class="badge {{ $customer->status ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $customer->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="d-flex justify-content-center gap-2">

                                        <!-- Edit -->
                                        <button type="button" class="btn btn-warning btn-sm edit-btn"
                                            data-id="{{ $customer->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <!-- Delete -->
                                        <button type="button" class="btn btn-danger btn-sm delete-btn"
                                            data-id="{{ $customer->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                        <!-- Toggle Status -->
                                        <button type="button"
                                            class="btn btn-sm toggle-status-btn {{ $customer->status ? 'btn-success' : 'btn-secondary' }}"
                                            data-id="{{ $customer->id }}">
                                            {{ $customer->status ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No customers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Modal for Adding Customer -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addCustomerForm" action="{{ route('customer.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editCustomerForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="edit_customer_id">

                        <div class="mb-3">
                            <label for="edit_first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="edit_phone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this customer?</p>
                    <input type="hidden" id="delete_customer_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        //  form submission via AJAX
        $('#addCustomerForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(data) {
                    if (data.success) {
                        $('#addCustomerModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        });

        // Delete with AJAX
        function deleteCustomer(customerId) {
            if (!confirm('Are you sure?')) return;

            $.ajax({
                url: "{{ url('admin/customers') }}/" + customerId,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function() {
                    $('#row-' + customerId).remove();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        }


        // Open Edit Modal & Prefill Data
        $(document).on('click', '.edit-btn', function() {
            let id = $(this).data('id');

            $.get("{{ route('customer.show', ':id') }}".replace(':id', id), function(customer) {
                // Prefill modal fields
                $('#edit_customer_id').val(customer.id);
                $('#edit_first_name').val(customer.first_name);
                $('#edit_last_name').val(customer.last_name);
                $('#edit_email').val(customer.email);
                $('#edit_phone').val(customer.phone);
                $('#edit_status').val(customer.status);

                // Set form action URL
                $('#editCustomerForm').attr('action', "{{ route('customer.update', ':id') }}".replace(
                    ':id', id));

                // Show modal
                $('#editCustomerModal').modal('show');
            });
        });


        // Submit Edit Form via AJAX
        $('#editCustomerForm').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(res) {
                    if (res.success) {
                        $('#editCustomerModal').modal('hide');
                        location.reload(); // later we can update row without reload
                    } else {
                        alert(res.message);
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        });


        // Toggle Status with AJAX
        $(document).on('click', '.toggle-status-btn', function() {
            let button = $(this);
            let customerId = button.data('id');

            $.ajax({
                url: "{{ url('admin/customers') }}/" + customerId + "/toggle-status",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.success) {
                        if (data.status === 'Active') {
                            button.removeClass('btn-secondary').addClass('btn-success').text('Active');
                            $('#badge-' + customerId).removeClass('bg-secondary').addClass('bg-success')
                                .text('Active');
                        } else {
                            button.removeClass('btn-success').addClass('btn-secondary').text(
                                'Inactive');
                            $('#badge-' + customerId).removeClass('bg-success').addClass('bg-secondary')
                                .text('Inactive');
                        }
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        });




        let deleteId = null;

        // Open Delete Confirmation Modal
        $(document).on('click', '.delete-btn', function() {
            deleteId = $(this).data('id');
            $('#delete_customer_id').val(deleteId);
            $('#deleteCustomerModal').modal('show');
        });

        // Handle Confirm Delete
        $('#confirmDeleteBtn').on('click', function() {
            let id = $('#delete_customer_id').val();

            $.ajax({
                url: "{{ route('customer.destroy', ':id') }}".replace(':id', id),
                type: 'POST',
                data: {
                    _method: 'DELETE'
                },
                success: function(res) {
                    if (res.success) {
                        $('#deleteCustomerModal').modal('hide');
                        $('#row-' + id).remove(); // remove row dynamically
                    } else {
                        alert(res.message);
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        });
    </script>
@endsection
