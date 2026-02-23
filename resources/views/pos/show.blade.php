<a href="{{ route('orders.list') }}" class="btn btn-outline-secondary mb-3">
    ← Back to Orders
</a>

<div class="card shadow border-0">

    {{-- Header --}}
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold">Order #{{ $order->order_number }}</h5>
            <small>Status:
                <span class="badge bg-light text-dark">
                    {{ ucfirst($order->status) }}
                </span>
            </small>
        </div>

        <div class="text-end">
            <small>Total</small>
            <h4 class="mb-0 fw-bold">₹{{ number_format($order->total,2) }}</h4>
        </div>
    </div>


    {{-- Customer --}}
    <div class="card-body border-bottom">
        <h6 class="text-muted mb-1">Customer</h6>
        <h5 class="fw-semibold">{{ $order->customer_name }}</h5>
    </div>


    {{-- Items --}}
    <div class="card-body">

        <h5 class="fw-bold mb-3">Items</h5>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th width="120">Qty</th>
                        <th width="150">Price</th>
                        <th width="150">Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                @forelse ($order->items as $item)
                    <tr>
                        <td class="fw-semibold">
                            {{ $item->product->title ?? 'N/A' }}
                        </td>

                        <td>
                            <span class="badge bg-secondary">
                                {{ $item->qty }}
                            </span>
                        </td>

                        <td>₹{{ number_format($item->price,2) }}</td>

                        <td class="fw-bold">
                            ₹{{ number_format($item->subtotal,2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            No items found
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

    </div>


    {{-- Footer Pay Section --}}
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">

        <div>
            <small class="text-muted">Payable Amount</small>
            <h4 class="fw-bold text-success mb-0">
                ₹{{ number_format($order->total,2) }}
            </h4>
        </div>

        @if($order->status == 'paid')
            <button class="btn btn-success btn-lg px-4" disabled>
                Paid
            </button>
        @else
            <button id="pay-btn" class="btn btn-success btn-lg px-4">
                Pay Now
            </button>
        @endif

    </div>

</div>


{{-- Razorpay --}}
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
document.getElementById('pay-btn')?.addEventListener('click', async function(e){

    const btn = this;
    btn.innerHTML = "Processing...";
    btn.disabled = true;

    let res = await fetch("{{ route('razorpay.order') }}", {
        method:"POST",
        headers:{
            "Content-Type":"application/json",
            "X-CSRF-TOKEN":"{{ csrf_token() }}"
        },
        body: JSON.stringify({
            order_id:"{{ $order->id }}"
        })
    });

    let data = await res.json();

    var options = {
        key: "{{ config('services.razorpay.key') }}",
        amount: "{{ $order->total * 100 }}",
        currency: "INR",
        name: "Acme Corp",
        description: "Order #{{ $order->order_number }}",
        order_id: data.id,
        callback_url: "{{ route('razorpay.callback') }}",

        prefill: {
            name: "{{ $order->customer_name }}",
            email: "{{ $order->customer_email ?? '' }}",
            contact: "{{ $order->customer_phone ?? '' }}"
        },

        theme: { color: "#0d6efd" }
    };

    var rzp = new Razorpay(options);

    rzp.on('payment.failed', function(){
        btn.innerHTML = "Pay Now";
        btn.disabled = false;
    });

    rzp.open();
    e.preventDefault();
});
</script>