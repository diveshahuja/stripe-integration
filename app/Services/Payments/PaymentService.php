<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use App\Services\BaseService;
use Illuminate\Http\Request;

class PaymentService
{

    private PaymentRepository $paymentRepository;
    public function __construct(PaymentRepository $paymentRepository){
        $this->paymentRepository = $paymentRepository;
    }

    public function create(Order $order, string $driver, float $amount): Payment{
        return $this->paymentRepository->create($order, $driver, $amount);
    }

    public function updateGatewayReferenceId(Payment $payment, string $gatewayReferenceId): Payment{
        return $this->paymentRepository->updateGatewayReferenceId($payment, $gatewayReferenceId);
    }

    public function updateStatus(Payment $payment, string $status): Payment{
        if (!in_array($status, [Payment::STATUS_PAID, Payment::STATUS_PENDING, Payment::STATUS_FAILED])) {
            throw new \InvalidArgumentException("Payment status '$status' does not exist");
        }
        if ($status == $payment->status){
            return $payment;
        }
        $payment->status = $status;
        $payment->save();
        return $payment;
    }
}
