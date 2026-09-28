<?php

namespace App\Helpers\Payments;

use App\Helpers\Payments\Contracts\PaymentGatewayContract;
use App\Helpers\Payments\Responses\PaymentResponse;
use App\Models\Order;

class PaypalPaymentGateway implements PaymentGatewayContract
{

    public function pay(Order $order): PaymentResponse
    {
        // TODO: Implement pay() method.
    }
}
