@extends('pos.layout.admin')

@section('content')
    <div class="container mt-3">
        <div class="row">
            <!-- Products Section -->
            <div class="col-lg-8 mb-3">
                <div class="card shadow-sm rounded-3">
                    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-bag-fill me-2"></i>Products</h5>
                        <input type="text" id="searchProduct" class="form-control w-50" placeholder="Search product...">
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <button class="btn btn-outline-secondary category-btn" data-category="all">All</button>
                            <button class="btn btn-outline-secondary category-btn" data-category="1">Furniture</button>
                            <button class="btn btn-outline-secondary category-btn" data-category="10">Appliances</button>
                        </div>
                        <div class="row" id="productList">
                            {{-- Initial display from Blade --}}
                            @foreach ($products as $p)
                                <div class="col-md-2 mb-3 product-item" data-category="{{ $p->category_id }}">
                                    <div class="card product-card" onclick="addToCart({{ $p->id }})">

                                        @if ($p->image)
                                            <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->title }}"
                                                class="img-thumbnail">
                                        @else
                                            <span class="text-muted">No Image</span>
                                        @endif

                                        <div class="card-body text-center">
                                            <h6>{{ $p->title }}</h6>
                                            <p class="text-success">${{ number_format($p->price, 2) }}</p>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Section -->
            <div class="col-lg-4">
                <div class="cart-summary">
                    <h5>Cart</h5>
                    <table class="table table-bordered cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Price*qty</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="cartItems">
                            <!-- Cart items added here -->
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <span id="cartTotal">$0.00</span>
                    </div>
                    <button class="btn btn-success w-100 mt-3" onclick="checkout()">PAYMENT</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Pass Laravel data into JS
        const products = @json($products);

        const productListEl = document.getElementById('productList');
        const cartItemsEl = document.getElementById('cartItems');
        const cartTotalEl = document.getElementById('cartTotal');
        var activeUser = "user1";
        let cart = {"user1": []};

        function addToCart(productId) {
            const product = products.find(p => p.id === productId);
            const activeCart = cart[activeUser]
            const cartItem = activeCart.find(item => item.id === productId);

            if (cartItem) {
                cartItem.qty += 1;
            } else {
                activeCart.push({
                    ...product,
                    qty: 1
                });
            }
            updateCart();
        }

        function removeFromCart(productId) {
            const activeCart = cart[activeUser]
            cart = cart.filter(item => item.id !== productId);
            updateCart();
        }

        function updateCart() {
            cartItemsEl.innerHTML = '';
            let total = 0;

             const activeCart = cart[activeUser]
            activeCart.forEach(item => {
                total += item.price * item.qty;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td>${item.title}</td>
                <td>${item.qty}</td>
                <td>${item.price}</td>
                <td>$${(item.price * item.qty).toFixed(2)}</td>
                <td><button class="btn btn-danger btn-sm" onclick="removeFromCart(${item.id})"><i class="bi bi-trash"></i></button></td>
            `;
                cartItemsEl.appendChild(tr);
            });

            cartTotalEl.innerText = `$${total.toFixed(2)}`;
        }

        function checkout() {
            const activeCart = cart[activeUser]
            if (activeCart.length === 0) {
                alert("Cart is empty!");
                return;
            }

            $.ajax({
                url: "{{ route('pos-page.store') }}",
                method: "POST",
                data: {
                    customer_name: "Walk-in Customer",
                    cart: activeCart,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.success) {
                        alert("Order placed successfully!");
                        window.location.href = "{{ route('pos-page.list') }}";
                    }
                },
                error: function(err) {
                    console.error(err);
                    alert("Something went wrong!");
                }
            });
        }


        // Category filtering
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const cat = btn.getAttribute('data-category');
                document.querySelectorAll('.product-item').forEach(item => {
                    if (cat === 'all' || item.getAttribute('data-category') == cat) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        // Search products
        document.getElementById('searchProduct').addEventListener('input', e => {
            const search = e.target.value.toLowerCase();
            document.querySelectorAll('.product-item').forEach(item => {
                const title = item.querySelector('h6').innerText.toLowerCase();
                item.style.display = title.includes(search) ? 'block' : 'none';
            });
        });
    </script>
@endpush
