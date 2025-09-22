@extends('pos.layout.admin')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Order #{{ $order->order_number }}</h4>
        </div>
        <div class="card-body">
            <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
            <p><strong>Status:</strong> {{ $order->status }}</p>
            <p><strong>Total:</strong> ${{ number_format($order->total, 2) }}</p>

            <h5>Items</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>

                    @forelse ($order->items as $item)
                        <tr>
                            <td>{{ $item->product->title ?? 'N/A' }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>${{ number_format($item->price, 2) }}</td>
                            <td>${{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No items found for this order</td>
                        </tr>
                    @endforelse
                    <tr>
                        <td colspan="4">
                            <button class="btn btn-secondary"> <a class="text text-decoration-none text-black" href="{{ route('orders.list') }}"> Back To List </a></button>
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>
    </div>
@endsection
