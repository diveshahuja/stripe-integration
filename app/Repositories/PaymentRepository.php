<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Payment;

class PaymentRepository
{
    public function create(Order $order, string $driver, float $amount): Payment{
        $payment = new Payment();
        $payment->order_id = $order->id;
        $payment->transaction_id = "T-" . $order->id . time() . mt_rand(1000, 9999);
        $payment->driver = $driver;
        $payment->amount = $amount;
        $payment->currency_code = "INR";
        $payment->save();
        return $payment;
    }

    public function updateGatewayReferenceId(Payment $payment, string $gatewayReferenceId): Payment{
        $payment->gateway_transaction_id = $gatewayReferenceId;
        $payment->save();
        return $payment;
    }
}
