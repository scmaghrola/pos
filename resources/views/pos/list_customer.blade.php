@extends('pos.layout.admin')

@section('content')
    <div class="container mt-5">
        <div class="card shadow-sm rounded-4 border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-people me-2"></i>Customers List</h5>
                <a href="{{ route('customer.add') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> <b>Add Customer</b>
                </a>
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
                                        <a href="{{ route('customer.edit', $customer->id) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <!-- Delete -->
                                        <button onclick="deleteCustomer({{ $customer->id }})"
                                            class="btn btn-danger btn-sm">
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
@endsection
@section('scripts')
    <script>
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
    </script>
@endsection
