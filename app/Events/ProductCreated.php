<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Product $product)
    {
    }

    public function broadcastOn(): array
    {
        return [new Channel('public-dashboard')];
    }

    public function broadcastAs(): string
    {
        return 'product.created';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'type' => 'product.created',
            'entity' => 'product',
            'entity_id' => $this->product->id,
            'timestamp' => now()->toISOString(),
            'message' => 'Товар создан',
            'data' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'price' => $this->product->price,
                'category_id' => $this->product->category_id,
            ],
        ];
    }
}
