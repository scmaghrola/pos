<div class="sidebar p-3">
    <h5 class="text-primary mb-4">Insight CRM</h5>
    <nav class="nav flex-column">
        <ul class="list-unstyled">

            <!-- Dashboard -->
            <li>
                <a href="{{ route('pos.dashboard') }}"
                    class="nav-link {{ Request::routeIs('pos.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>

            <!-- Category -->
            <li>
                @php
                    // Check if either Add Category or List Category route is active
                    $categoryActive = Request::routeIs('category.add') || Request::routeIs('category.list');
                @endphp
                <a class="nav-link d-flex justify-content-between align-items-center {{ $categoryActive ? '' : '' }}"
                    data-bs-toggle="collapse" href="#categoryMenu" role="button"
                    aria-expanded="{{ $categoryActive ? 'true' : 'false' }}">
                    <span><i class="bi bi-folder2-open me-2"></i>Category</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>

                <div class="collapse ms-4 {{ $categoryActive ? 'show' : '' }}" id="categoryMenu">
                    <ul class="list-unstyled">
                        <li>
                            <a href="{{ route('category.add') }}"
                                class="nav-link {{ Request::routeIs('category.add') ? 'active' : '' }}">
                                Add
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('category.list') }}"
                                class="nav-link {{ Request::routeIs('category.list') ? 'active' : '' }}">
                                List
                            </a>
                        </li>
                    </ul>
                </div>
            </li>


            <!-- Products -->
            <li>
                @php
                    $productActive = Request::routeIs('pos.product') || Request::routeIs('list.product');
                @endphp
                <a class="nav-link d-flex justify-content-between align-items-center {{ $productActive ? '' : '' }}"
                    data-bs-toggle="collapse" href="#productsMenu" role="button"
                    aria-expanded="{{ $productActive ? 'true' : 'false' }}">
                    <span><i class="bi bi-bag me-2"></i>Products</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <div class="collapse ms-4 {{ $productActive ? 'show' : '' }}" id="productsMenu">
                    <ul class="list-unstyled">
                        <li>
                            <a href="{{ route('pos.products.create') }}"
                                class="nav-link {{ Request::routeIs('pos.products.create') ? 'active' : '' }}">Add</a>
                        </li>
                        <li>
                            <a href="{{ route('pos.products.index') }}"
                                class="nav-link {{ Request::routeIs('pos.products.index') ? 'active' : '' }}">List</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Customers -->
            <li>
                @php
                    // Check if either Add Customer or List Customer route is active
                    $customerActive = Request::routeIs('customer.add') || Request::routeIs('customer.list');
                @endphp
                <a class="nav-link d-flex justify-content-between align-items-center {{ $customerActive ? '' : '' }}"
                    data-bs-toggle="collapse" href="#customersMenu" role="button"
                    aria-expanded="{{ $customerActive ? 'true' : 'false' }}">
                    <span><i class="bi bi-people me-2"></i>Customers</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>

                <div class="collapse ms-4 {{ $customerActive ? 'show' : '' }}" id="customersMenu">
                    <ul class="list-unstyled">
                        <li>
                            <a href="{{ route('customer.add') }}"
                                class="nav-link {{ Request::routeIs('customer.add') ? 'active' : '' }}">
                                Add
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('customer.list') }}"
                                class="nav-link {{ Request::routeIs('customer.list') ? 'active' : '' }}">
                                List
                            </a>
                        </li>
                    </ul>
                </div>
            </li>


            <!-- Orders -->
            <li>
                <a href="{{ route('orders.list') }}"
                    class="nav-link  {{ Request::routeIs('orders.list') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event me-2"></i>Orders
                </a>
            </li>

            <!-- POS -->
            <li>
                <a href="{{ route('pos-page.list') }}"
                    class="nav-link {{ Request::routeIs('pos-page.list') ? 'active' : '' }}">
                    <i class="bi bi-cart2 me-2"></i>POS
                </a>
            </li>

            <!-- Settings -->
            <li>
                <a href="#" class="nav-link">
                    <i class="bi bi-gear me-2"></i>Settings
                </a>
            </li>
        </ul>
    </nav>
</div>
