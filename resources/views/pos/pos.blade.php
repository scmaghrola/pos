@extends('pos.layout.admin')

@push('styles')
    <style>
        .product-card {
            width: 100%;
            max-width: 180px;
        }

        .fixed-img {
            height: 120px;
            object-fit: contain;
        }
    </style>
@endpush

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
                                    <div class="card product-card h-100 d-flex flex-column text-center"
                                        onclick="addToCart({{ $p->id }})" style="min-height: 260px;">

                                        @if ($p->image)
                                            <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->title }}"
                                                class="card-img-top img-fluid fixed-img">
                                        @else
                                            <span class="text-muted mt-3">No Image</span>
                                        @endif

                                        <div class="card-body d-flex flex-column justify-content-between">
                                            <h6 class="card-title text-truncate">{{ $p->title }}</h6>
                                            <p class="text-success mb-0">${{ number_format($p->price, 2) }}</p>
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

                <!-- Trigger Button -->
                <button class="btn btn-primary w-100 mb-3" data-bs-toggle="modal" data-bs-target="#customerModal">
                    Select Customer
                </button>

                <!-- Selected customer preview -->
                <div class="alert alert-info" id="selectedCustomerBox">
                    <strong>Customer:</strong> <span id="selectedCustomerName"></span>
                </div>

                <!-- Customer Modal -->
                <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="customerModalLabel">Select Customer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="list-group">
                                    @forelse ($customers as $customer)
                                        <button type="button"
                                            class="list-group-item list-group-item-action customer-select"
                                            data-id="{{ $customer['id'] }}"
                                            data-name="{{ $customer['first_name'] }} {{ $customer['last_name'] }}">
                                            {{ $customer['first_name'] }} {{ $customer['last_name'] }}
                                        </button>
                                    @empty
                                        <p class="text-muted">No customers found.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Customer Modal -->



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
    {{-- <script>
        let selectedCustomer = {
            id: null,
            name: "Walk-in Customer"
        };

        // Handle customer selection from modal
        document.querySelectorAll('.customer-select').forEach(btn => {
            btn.addEventListener('click', function() {
                selectedCustomer.id = this.dataset.id;
                selectedCustomer.name = this.dataset.name;

                document.getElementById('selectedCustomerName').innerText = selectedCustomer.name;

                // Close modal after selection
                var modal = bootstrap.Modal.getInstance(document.getElementById('customerModal'));
                modal.hide();
            });
        });

        // Update checkout function to send selected customer
        function checkout() {
            const activeCart = cart[activeUser];
            if (activeCart.length === 0) {
                alert("Cart is empty!");
                return;
            }

            $.ajax({
                url: "{{ route('pos-page.store') }}",
                method: "POST",
                data: {
                    customer_id: selectedCustomer.id,
                    customer_name: selectedCustomer.name,
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

        document.addEventListener('DOMContentLoaded', function() {
            const radios = document.getElementsById('radiobtn');

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    console.log('Selected value:', this.value);
                });
            });
        });

        // Pass Laravel data into JS
        const products = @json($products);

        const productListEl = document.getElementById('productList');
        const cartItemsEl = document.getElementById('cartItems');
        const cartTotalEl = document.getElementById('cartTotal');
        var activeUser = "user1";
        let cart = {
            "user1": []
        };

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
            const activeCart = cart[activeUser];
            cart[activeUser] = activeCart.filter(item => item.id !== productId);
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
    </script> --}}


    <script>
        // ======================== COOKIE HELPERS ========================
        function setCookie(name, value, days = 1) {
            const d = new Date();
            d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
            let expires = "expires=" + d.toUTCString();
            document.cookie = name + "=" + encodeURIComponent(value) + ";" + expires + ";path=/";
        }

        function getCookie(name) {
            let cname = name + "=";
            let decodedCookie = decodeURIComponent(document.cookie);
            let ca = decodedCookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i].trim();
                if (c.indexOf(cname) == 0) {
                    return c.substring(cname.length, c.length);
                }
            }
            return "";
        }

        // ======================== GLOBAL VARS ========================
        let selectedCustomer = {
            id: null,
            name: "Walk-in Customer"
        };
        let cart = {}; // per-customer carts
        let activeUser = null;

        // Load carts from cookie when page loads
        document.addEventListener("DOMContentLoaded", () => {
            const savedCart = getCookie("pos_cart");
            if (savedCart) {
                try {
                    cart = JSON.parse(savedCart);
                } catch {
                    cart = {};
                }
            }
        });

        // Save carts to cookie
        function saveCart() {
            setCookie("pos_cart", JSON.stringify(cart), 1);
        }

        // ======================== CUSTOMER SELECT ========================
        document.querySelectorAll('.customer-select').forEach(btn => {
            btn.addEventListener('click', function() {
                selectedCustomer.id = this.dataset.id;
                selectedCustomer.name = this.dataset.name;

                activeUser = selectedCustomer.id;

                if (!cart[activeUser]) {
                    cart[activeUser] = [];
                }

                document.getElementById('selectedCustomerName').innerText = selectedCustomer.name;
                updateCart();
                saveCart();

                var modal = bootstrap.Modal.getInstance(document.getElementById('customerModal'));
                modal.hide();
            });
        });

        // ======================== CART FUNCTIONS ========================
        const products = @json($products);

        function addToCart(productId) {
            if (!activeUser) {
                alert("Please select a customer first!");
                return;
            }

            const product = products.find(p => p.id === productId);
            const activeCart = cart[activeUser];
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
            saveCart();
        }

        function removeFromCart(productId) {
            if (!activeUser) return;
            cart[activeUser] = cart[activeUser].filter(item => item.id !== productId);
            updateCart();
            saveCart();
        }

        function updateCart() {
            const cartItemsEl = document.getElementById('cartItems');
            const cartTotalEl = document.getElementById('cartTotal');

            cartItemsEl.innerHTML = '';
            let total = 0;

            const activeCart = activeUser ? cart[activeUser] : [];
            activeCart.forEach(item => {
                total += item.price * item.qty;
                const tr = document.createElement('tr');
                tr.innerHTML = `
            <td>${item.title}</td>
            <td>${item.qty}</td>
            <td>${item.price}</td>
            <td>$${(item.price * item.qty).toFixed(2)}</td>
            <td>
                <button class="btn btn-danger btn-sm" onclick="removeFromCart(${item.id})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
                cartItemsEl.appendChild(tr);
            });

            cartTotalEl.innerText = `$${total.toFixed(2)}`;
        }

        // ======================== CHECKOUT ========================
        function checkout() {
            if (!activeUser || cart[activeUser].length === 0) {
                alert("Cart is empty!");
                return;
            }

            $.ajax({
                url: "{{ route('pos-page.store') }}",
                method: "POST",
                data: {
                    customer_id: selectedCustomer.id,
                    customer_name: selectedCustomer.name,
                    cart: cart[activeUser],
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.success) {
                        alert("Order placed successfully!");
                        // clear only this customer's cart
                        cart[activeUser] = [];
                        updateCart();
                        saveCart();
                        window.location.href = "{{ route('pos-page.list') }}";
                    }
                },
                error: function(err) {
                    console.error(err);
                    alert("Something went wrong!");
                }
            });
        }

        // ======================== CATEGORY & SEARCH ========================
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

        document.getElementById('searchProduct').addEventListener('input', e => {
            const search = e.target.value.toLowerCase();
            document.querySelectorAll('.product-item').forEach(item => {
                const title = item.querySelector('h6').innerText.toLowerCase();
                item.style.display = title.includes(search) ? 'block' : 'none';
            });
        });
    </script>
@endpush
