<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function broadcastOn(): array
    {
        return [new Channel('public-dashboard')];
    }

    public function broadcastAs(): string
    {
        return 'order.created';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'type' => 'order.created',
            'entity' => 'order',
            'entity_id' => $this->order->id,
            'timestamp' => now()->toISOString(),
            'message' => 'Заказ создан',
            'data' => [
                'id' => $this->order->id,
                'user_id' => $this->order->user_id,
                'status' => $this->order->status,
                'total' => $this->order->total,
            ],
        ];
    }
}
