@extends('pos.layout.layout')

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
                        <tbody id="product-tbody">
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
                                            <img src="{{ asset('storage/products/' . $product->image) }}"
                                                alt="{{ $product->title }}" width="100">
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
                                            <a href="{{ route('pos.products.edit', $product->id) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form class="deleteProductForm" data-id="{{ $product->id }}"
                                                onsubmit="return false;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
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
                <div id="product-pagination" class="d-flex justify-content-center mt-3">
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Route and asset helpers
        var editRouteTemplate = '{{ route('pos.products.edit', 'ID') }}';
        var destroyRouteTemplate = '{{ route('pos.products.destroy', ':id') }}';
        var toggleRouteTemplate = '{{ route('pos.products.toggleStatus', ':id') }}';
        var assetBase = '{{ asset('') }}';
        var csrf = $('meta[name="csrf-token"]').attr('content');

        $(document).ready(function() {
            // AJAX Pagination (delegated event)
            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                getProducts(page);
            });

            // Delete Product (delegated event)
            $(document).on('submit', '.deleteProductForm', function(event) {
                event.preventDefault();
                var form = $(this);
                var productId = form.data('id');

                if (confirm('Are you sure you want to delete this product?')) {
                    $.ajax({
                        url: destroyRouteTemplate.replace(':id', productId),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                // Refresh current page
                                var currentPage = new URLSearchParams(window.location.search)
                                    .get('page') || 1;
                                getProducts(currentPage);
                            }
                        },
                        error: function() {
                            alert('An error occurred. Please try again.');
                        }
                    });
                }
            });

            // Toggle Product Status (delegated event)
            $(document).on('submit', '.toggleStatusForm', function(event) {
                event.preventDefault();
                var form = $(this);
                var productId = form.data('id');

                $.ajax({
                    url: toggleRouteTemplate.replace(':id', productId),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            // Refresh current page
                            var currentPage = new URLSearchParams(window.location.search).get(
                                'page') || 1;
                            getProducts(currentPage);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });

        function getProducts(page) {
            console.log('Fetching page ' + page); // Debug log
            $.ajax({
                url: '{{ route('pos.products.index') }}?page=' + page,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var tbody = $('#product-tbody');
                    tbody.empty();

                    data.products.forEach(function(product, index) {
                        var rowNumber = index + 1 + (data.current_page - 1) * data.per_page;
                        var category = product.category ? product.category.name : '-';
                        var price = '<span class="badge bg-success">$' + parseFloat(product.price)
                            .toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }) + '</span>';
                        var comparePrice = product.compare_price ?
                            '<span class="badge bg-primary">$' + parseFloat(product.compare_price)
                            .toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }) + '</span>' :
                            '<span class="text-muted">-</span>';
                        var sku = product.sku || '-';
                        var weight = product.weight || '-';
                        var image = product.image ?
                            '<img src="' + assetBase + 'storage/products/' + product.image + '" alt="' +
                            product.title + '" width="100">' :
                            '<span class="text-muted">No Image</span>';
                        var statusBadge = '<span class="badge ' + (product.status ? 'bg-success' :
                                'bg-secondary') + '">' + (product.status ? 'Active' : 'Inactive') +
                            '</span>';
                        var actions = `
                            <div class="d-flex justify-content-center gap-2">
                                <a href="${editRouteTemplate.replace('ID', product.id)}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form class="deleteProductForm" data-id="${product.id}" onsubmit="return false;">
                                    <input type="hidden" name="_token" value="${csrf}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <form class="toggleStatusForm" data-id="${product.id}" onsubmit="return false;">
                                    <input type="hidden" name="_token" value="${csrf}">
                                    <input type="hidden" name="_method" value="PATCH">
                                    <button type="submit" class="btn ${product.status ? 'btn-success' : 'btn-secondary'} btn-sm">
                                        ${product.status ? 'Active' : 'Inactive'}
                                    </button>
                                </form>
                            </div>
                        `;

                        var row = `
                            <tr>
                                <td>${rowNumber}</td>
                                <td>${product.title}</td>
                                <td>${category}</td>
                                <td>${price}</td>
                                <td>${comparePrice}</td>
                                <td>${sku}</td>
                                <td>${weight}</td>
                                <td>${image}</td>
                                <td>${statusBadge}</td>
                                <td>${actions}</td>
                            </tr>
                        `;
                        tbody.append(row);
                    });

                    $('#product-pagination').html(data.pagination);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error); // Log details
                    alert('An error occurred while loading products.');
                }
            });
        }
    </script>
@endsection
