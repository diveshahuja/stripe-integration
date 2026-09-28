<?php

namespace App\Helpers\Payments;

use App\Helpers\Payments\Contracts\PaymentGatewayContract;
use App\Helpers\Payments\Responses\PaymentResponse;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payments\PaymentService;
use Stripe\StripeClient;

class StripePaymentGateway implements PaymentGatewayContract
{
    private array $config;
    private PaymentService $paymentService;

    public function __construct(array $config, PaymentService $paymentService)
    {
        $this->config = $config;
        $this->paymentService = $paymentService;
    }

    public function pay(Order $order): PaymentResponse
    {
        if ($order->total <= 0) {
            throw new \InvalidArgumentException("Order total must be greater than 0", 422);
        }
        $payment = $this->paymentService->create($order, "stripe", $order->total);
        $stripe = new StripeClient(['api_key' => $this->config['key']]);
        $paymentResponse = new PaymentResponse();
        $session = $stripe->checkout->sessions->create([
            'success_url' => route('orders.verify-order', ['driver' => 'stripe', 'transaction_id' => $payment->transaction_id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'line_items' => $this->setLineItemsForSubscription($order),
            'mode' => 'subscription',
        ]);
        $this->paymentService->updateGatewayReferenceId($payment, $session->id);
        $paymentResponse->setRedirectUrl($session->url);
        return $paymentResponse;
    }

    private function setLineItemsForOneTimePayment(Order $order): array
    {
        $lineItems = [];
        if ($order->items->isEmpty()) {
            throw new \InvalidArgumentException("Order items are not present in this order.", 422);
        }
        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'inr',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => (int)($item->total * 100),
                ],
                'quantity' => $item->quantity,
            ];
        }
        return $lineItems;
    }

    private function setLineItemsForSubscription(Order $order): array
    {
        if ($order->items->isEmpty()) {
            throw new \InvalidArgumentException("Order items are not present in this order.", 422);
        }
        $lineItems = [];
        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'inr',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => (int)($item->total * 100),
                    'recurring' => [
                        'interval' => 'month',
                    ],
                ],
                'quantity' => $item->quantity,
            ];
        }
        return $lineItems;
    }

    public function verifyPayment(Payment $payment): PaymentResponse
    {
        $stripe = new StripeClient(['api_key' => $this->config['key']]);
        $session = $stripe->checkout->sessions->retrieve($payment->gateway_transaction_id);
        $paymentResponse = new PaymentResponse();
        if (!empty($session)) {
            if ($session->status == 'complete' && $session->payment_status == 'paid') {
                $paymentResponse->setSuccess(true);
                $this->paymentService->updateStatus($payment, Payment::STATUS_PAID);
            }
        } else {
            $paymentResponse->setSuccess(false);
            $this->paymentService->updateStatus($payment, Payment::STATUS_FAILED);
        }
        return $paymentResponse;
    }
}
