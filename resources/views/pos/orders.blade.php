@extends('pos.layout.layout')

@section('content')
    <div class="flex-grow-1 content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Orders</h3>
            <button class="btn btn-primary"> <a href="{{ route('pos-page.list') }}" class="btn btn-primary">+ New Order</a>
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTable">

                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $order->customer_name }}</td>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <span
                                        class="badge 
                {{ $order->status == 'Completed' ? 'bg-success' : ($order->status == 'Pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td>${{ number_format($order->total, 2) }}</td>
                                <td>
                                    <a href="{{ route('orders.view', $order->id) }}" class="btn btn-sm btn-info text-white">
                                        View
                                    </a>

                                    <form action="{{ route('orders.delete', $order->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this order?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>

                            </tr>
                        @endforeach

                </table>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('scripts')
@endsection
