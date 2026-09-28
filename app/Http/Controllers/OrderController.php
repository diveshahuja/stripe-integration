<?php

namespace App\Http\Controllers;

use App\Helpers\Payments\PaymentHelper;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderService $orderService;
    private PaymentHelper $paymentHelper;
    public function __construct(OrderService $orderService, PaymentHelper $paymentHelper){
        $this->orderService = $orderService;
        $this->paymentHelper = $paymentHelper;
    }

    public function store(Request $request){
        try{
            $order = $this->orderService->store($request);
            $paymentResponse = $this->paymentHelper->driver($request->payment_method)->pay($order);
            if (!empty($paymentResponse->getRedirectUrl())){
                return redirect($paymentResponse->getRedirectUrl());
            }
        }catch (\InvalidArgumentException $exception){
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }

    public function verifyOrder(string $driver, string $transactionId, Request $request){
        $payment = Payment::where(['driver' => $driver, 'transaction_id' => $transactionId])->first();
        abort_if(!$payment, 404);
        $paymentResponse = $this->paymentHelper->driver($driver)->verifyPayment($payment);
        if ($paymentResponse->isSuccess()){
            $this->orderService->updateStatus(Order::where('id', $payment->order_id)->first(), Order::STATUS_CONFIRMED);
        }else{
            $this->orderService->updateStatus(Order::where('id', $payment->order_id)->first(), Order::STATUS_CANCELLED);
        }
    }
}
