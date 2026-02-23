<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\Order;

class PaymentController extends Controller
{
    public function createOrder(Request $request)
    {
        $order = Order::findOrFail($request->order_id);

        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        $razorpayOrder = $api->order->create([
            'receipt' => $order->order_number,
            'amount' => $order->total * 100,
            'currency' => 'INR'
        ]);

        return response()->json([
            'id' => $razorpayOrder['id']
        ]);
    }

    public function callback(Request $request)
    {
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ]);

            $order = Order::where('order_number',$request->razorpay_order_id)->first();

            if($order){
                $order->status = 'paid';
                $order->save();
            }

            return redirect()->route('orders.list')->with('success','Payment successful');

        } catch (\Exception $e) {
            return redirect()->route('orders.list')->with('error','Payment verification failed');
        }
    }
}