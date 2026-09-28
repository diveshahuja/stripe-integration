<?php

namespace App\Helpers\Payments;

use App\Helpers\Payments\Contracts\PaymentGatewayContract;
use App\Helpers\Payments\Responses\PaymentResponse;
use App\Models\Order;

class RazorpayPaymentGateway implements PaymentGatewayContract
{

    public function pay(Order $order): PaymentResponse
    {

    }
}
