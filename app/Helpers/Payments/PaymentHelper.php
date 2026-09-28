<?php

namespace App\Helpers\Payments;

use App\Helpers\Payments\Responses\PaymentResponse;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payments\PaymentService;

class PaymentHelper
{
    private PaymentService $paymentService;
    public function __construct(PaymentService $paymentService){
        $this->paymentService = $paymentService;
    }
    private object $paymentGateway;
    public function driver(string $driver): PaymentHelper{
        $config = config('payments.gateways');
        if (!in_array($driver, array_keys($config))){
            throw new \InvalidArgumentException("Driver [$driver] not supported", 422);
        }
        $this->paymentGateway = match($driver){
            "stripe" => new StripePaymentGateway($config["stripe"], $this->paymentService),
            "razorpay" => new RazorPayPaymentGateway($config["razorpay"]),
            "paypal" => new PayPalPaymentGateway($config["paypal"]),
            default => throw new \InvalidArgumentException("Driver [$driver] not supported", 422)
        };
        return $this;
    }

    public function pay(Order $order): PaymentResponse{
        return $this->paymentGateway->pay($order);
    }

    public function verifyPayment(Payment $payment): PaymentResponse{
        return $this->paymentGateway->verifyPayment($payment);
    }
}
