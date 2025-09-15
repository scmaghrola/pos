@extends('pos.layout.admin')

@section('content')
    <div class="container mt-5">
        <div class="card shadow-sm rounded-4 border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-folder2-open me-2"></i>Categories List</h5>
                <a href="{{ route('category.add') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> <b>Add Category</b>
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Parent</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @forelse($categories as $category)
                                <tr id="categoryRow{{ $category->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->parent->name ?? '-' }}</td>
                                    <td>
                                        @if ($category->image)
                                            <img src="{{ asset('storage/' . $category->image) }}"
                                                alt="{{ $category->name }}" class="img-thumbnail"
                                                style="width:60px;height:60px;">
                                        @else
                                            <span class="text-muted">No Image</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $category->status ? 'bg-success' : 'bg-secondary' }}"
                                            id="statusBadge{{ $category->id }}">
                                            {{ $category->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('category.edit', $category->id) }}"
                                            class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>

                                        <button class="btn btn-sm btn-danger deleteCategory"
                                            data-id="{{ $category->id }}"><i class="bi bi-trash"></i></button>

                                        <button
                                            class="btn btn-sm toggleStatus {{ $category->status ? 'btn-success' : 'btn-secondary' }}"
                                            data-id="{{ $category->id }}">
                                            {{ $category->status ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Delete Category
            $('.deleteCategory').on('click', function() {
                if (!confirm('Are you sure you want to delete this category?')) return;

                let id = $(this).data('id');

                $.ajax({
                    url: '{{ route("categories.destroy", ":id") }}'.replace(':id', id),
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        $('#categoryRow' + id).remove();
                        alert(res.message || 'Category deleted successfully.');
                    },
                    error: function(xhr) {
                        console.log('Delete Error:', xhr.responseText);
                        alert('Error deleting category: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    }
                });
            });

            // Toggle Status
            $('.toggleStatus').on('click', function() {
                let button = $(this);
                let id = button.data('id');

                $.ajax({
                    url: '{{ route("category.toggleStatus", ":id") }}'.replace(':id', id),
                    type: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.status) {
                            button.removeClass('btn-secondary').addClass('btn-success').text('Active');
                            $('#statusBadge' + id).removeClass('bg-secondary').addClass('bg-success').text('Active');
                        } else {
                            button.removeClass('btn-success').addClass('btn-secondary').text('Inactive');
                            $('#statusBadge' + id).removeClass('bg-success').addClass('bg-secondary').text('Inactive');
                        }
                        alert(res.message || 'Status updated successfully.');
                    },
                    error: function(xhr) {
                        console.log('Toggle Status Error:', xhr.responseText);
                        alert('Error updating status: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    }
                });
            });
        });
    </script>
@endsection