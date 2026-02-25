<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order, public string $message = 'Заказ обновлен')
    {
    }

    public function broadcastOn(): array
    {
        return [new Channel('public-dashboard')];
    }

    public function broadcastAs(): string
    {
        return 'order.updated';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'type' => 'order.updated',
            'entity' => 'order',
            'entity_id' => $this->order->id,
            'timestamp' => now()->toISOString(),
            'message' => $this->message,
            'data' => [
                'id' => $this->order->id,
                'user_id' => $this->order->user_id,
                'status' => $this->order->status,
                'total' => $this->order->total,
            ],
        ];
    }
}
