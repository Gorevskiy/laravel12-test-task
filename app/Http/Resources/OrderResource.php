<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Order */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'total' => $this->total,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'items' => $this->whenLoaded('products', function () {
                return $this->products->map(function ($product) {
                    $quantity = (int) $product->pivot->quantity;
                    $priceAtPurchase = (float) $product->pivot->price_at_purchase;

                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $quantity,
                        'price_at_purchase' => number_format($priceAtPurchase, 2, '.', ''),
                        'line_total' => number_format($quantity * $priceAtPurchase, 2, '.', ''),
                    ];
                });
            }),
        ];
    }
}
