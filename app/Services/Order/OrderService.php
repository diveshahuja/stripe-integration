<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\BaseService;
use Illuminate\Http\Request;

class OrderService extends BaseService
{
    private OrderRepository $orderRepository;
    public function __construct(OrderRepository $orderRepository){
        $this->orderRepository = $orderRepository;
    }

    public function store(Request $request): Order{
        return $this->orderRepository->store($request);
    }

    public function updateStatus(Order $order, $status): Order{
        return $this->orderRepository->updateStatus($order, $status);
    }
}
