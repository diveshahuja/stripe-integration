<?php

namespace App\Helpers\Payments\Contracts;

use App\Helpers\Payments\Responses\PaymentResponse;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

interface PaymentGatewayContract
{
    public function pay(Order $order): PaymentResponse;
    public function verifyPayment(Payment $payment): PaymentResponse;
}
