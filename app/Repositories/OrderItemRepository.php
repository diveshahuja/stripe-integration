<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

trait OrderItemRepository
{
    public function storeItems(Order $order, Request $request): Order{
        $orderItem = new OrderItem();
        $orderItem->order_id = $order->id;
        $orderItem->name = "Blue Jeans";
        $orderItem->mrp = 100;
        $orderItem->price = 90;
        $orderItem->discount = 10;
        $orderItem->quantity = 2;
        $orderItem->sub_total = $orderItem->price * $orderItem->quantity;
        $orderItem->total = $orderItem->sub_total + $orderItem->tax - $orderItem->discount;
        $orderItem->save();

        $orderItem = new OrderItem();
        $orderItem->order_id = $order->id;
        $orderItem->name = "Red Tshirt";
        $orderItem->mrp = 50;
        $orderItem->price = 45;
        $orderItem->discount = 5;
        $orderItem->quantity = 1;
        $orderItem->sub_total = $orderItem->price * $orderItem->quantity;
        $orderItem->total = $orderItem->sub_total + $orderItem->tax - $orderItem->discount;
        $orderItem->save();
        $order->load('items');
        $this->reCalculateItemsTaxes($order);
        $this->reCalculateItemsTotal($order);
        return $order;
    }

    private function reCalculateItemsTaxes(Order $order): void{
        if ($order->items->isEmpty()){
            throw new \InvalidArgumentException("Items not found", 500);
        }
        $taxPercentage = 5;
        foreach ($order->items as $item){
            $item->tax = (($item->price * $taxPercentage) / 100) * $item->quantity;
            $item->save();
        }
    }

    private function reCalculateItemsTotal(Order $order): void{
        if ($order->items->isEmpty()){
            throw new \InvalidArgumentException("Items not found", 500);
        }
        foreach ($order->items as $item){
            $item->total = $item->sub_total + $item->tax - $item->discount;
            $item->save();
        }
    }

    private function updateOrderItemStatus(OrderItem $orderItem, string $status): OrderItem{
        if (!in_array($status, [OrderItem::STATUS_CANCELLED, OrderItem::STATUS_DELIVERED, OrderItem::STATUS_READY_TO_SHIP, OrderItem::STATUS_RTO, OrderItem::STATUS_SHIPPED])){
            throw new \InvalidArgumentException("Invalid status $status", 500);
        }
        if ($status == $orderItem->status){
            return $orderItem;
        }
        $orderItem->status = $status;
        $orderItem->save();
        return $orderItem;
    }
}
