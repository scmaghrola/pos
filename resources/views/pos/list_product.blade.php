@extends('pos.layout.admin')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm border-0 rounded-4s">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-bag-check me-2"></i> Products List</h5>
                <a href="{{ route('pos.products.create') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> <b>Add Product</b>
                </a>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Compare Price</th>
                                <th>SKU</th>
                                <th>Weight</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $product->title }}</td>
                                    <td>{{ $product->category ? $product->category->name : '-' }}</td>
                                    <td><span class="badge bg-success">${{ number_format($product->price, 2) }}</span></td>
                                    <td>
                                        @if ($product->compare_price)
                                            <span
                                                class="badge bg-primary">${{ number_format($product->compare_price, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->sku ?? '-' }}</td>
                                    <td>{{ $product->weight ?? '-' }}</td>
                                    <td>
                                        @if ($product->image)
                                            <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->title }}" width="100">

                                            {{-- <img src="@{{ asset('storage/' . $product->image) }}"
                                                alt="@{{ $product->title }}" class="img-thumbnail" width="50"> --}}
                                        @else
                                            <span class="text-muted">No Image</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $product->status ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $product->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            {{-- Edit --}}
                                            <a href="{{ route('pos.products.edit', $product->id) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            {{-- Delete --}}
                                            <form class="deleteProductForm" data-id="{{ $product->id }}"
                                                onsubmit="return false;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>

                                            {{-- Status Toggle --}}
                                            <form class="toggleStatusForm" data-id="{{ $product->id }}"
                                                onsubmit="return false;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="btn {{ $product->status ? 'btn-success' : 'btn-secondary' }} btn-sm">
                                                    {{ $product->status ? 'Active' : 'Inactive' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            // Delete Product
            $('.deleteProductForm').on('submit', function() {
                var form = $(this);
                var productId = form.data('id');

                if (confirm('Are you sure you want to delete this product?')) {
                    $.ajax({
                        url: '{{ route('pos.products.destroy', ':id') }}'.replace(':id',
                            productId),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                location.reload();
                            }
                        },
                        error: function() {
                            alert('An error occurred. Please try again.');
                        }
                    });
                }
            });

            // Toggle Product Status
            $('.toggleStatusForm').on('submit', function() {
                var form = $(this);
                var productId = form.data('id');

                $.ajax({
                    url: '{{ route('pos.products.toggleStatus', ':id') }}'.replace(':id',
                        productId),
                    type: 'POST', // let method spoofing handle PATCH
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText); // log exact error
                        alert('An error occurred. Please try again.');
                    }
                });
            });

        });
    </script>
@endsection
