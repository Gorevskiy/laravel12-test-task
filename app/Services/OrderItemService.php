<?php

namespace App\Services;

use App\Events\OrderUpdated;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrderItemService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function addOrUpdateItem(Order $order, array $data): Order
    {
        return DB::transaction(function () use ($order, $data) {
            $product = Product::query()->findOrFail($data['product_id']);

            $existingItem = OrderItem::query()
                ->where('order_id', $order->id)
                ->where('product_id', $product->id)
                ->first();

            $priceAtPurchase = $existingItem?->price_at_purchase ?? $product->price;

            OrderItem::query()->updateOrCreate(
                ['order_id' => $order->id, 'product_id' => $product->id],
                [
                    'quantity' => (int) $data['quantity'],
                    'price_at_purchase' => $priceAtPurchase,
                ]
            );

            $this->recalculateTotal($order);

            $order->load(['products', 'user']);
            $this->dispatchSafely(new OrderUpdated($order, 'Позиция заказа добавлена или обновлена'));

            return $order;
        });
    }

    public function detachItem(Order $order, int $productId): Order
    {
        return DB::transaction(function () use ($order, $productId) {
            $item = OrderItem::query()
                ->where('order_id', $order->id)
                ->where('product_id', $productId)
                ->first();

            if (! $item) {
                throw (new ModelNotFoundException())->setModel(OrderItem::class);
            }

            $item->delete();

            $this->recalculateTotal($order);

            $order->load(['products', 'user']);
            $this->dispatchSafely(new OrderUpdated($order, 'Позиция заказа удалена'));

            return $order;
        });
    }

    protected function recalculateTotal(Order $order): void
    {
        $total = (float) OrderItem::query()
            ->where('order_id', $order->id)
            ->selectRaw('COALESCE(SUM(quantity * price_at_purchase), 0) as total')
            ->value('total');

        $order->update([
            'total' => number_format($total, 2, '.', ''),
        ]);
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
