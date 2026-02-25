<?php

namespace App\Services;

use App\Events\OrderCreated;
use App\Events\OrderUpdated;
use App\Models\Order;
use Throwable;

class OrderService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Order
    {
        /** @var Order $order */
        $order = Order::query()->create([
            'user_id' => $data['user_id'],
            'status' => $data['status'] ?? 'new',
            'total' => 0,
        ]);

        $order->load(['products', 'user']);
        $this->dispatchSafely(new OrderCreated($order));

        return $order;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateStatus(Order $order, array $data): Order
    {
        $order->update(['status' => $data['status']]);
        $order->load(['products', 'user']);

        $this->dispatchSafely(new OrderUpdated($order, 'Статус заказа обновлен'));

        return $order;
    }

    public function delete(Order $order): void
    {
        $order->delete();
    }

    protected function dispatchSafely(object $event): void
    {
        try {
            event($event);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
