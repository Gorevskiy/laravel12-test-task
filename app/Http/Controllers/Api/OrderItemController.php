<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderItemStoreRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderItemService;

class OrderItemController extends Controller
{
    public function __construct(private readonly OrderItemService $orderItemService)
    {
    }

    public function store(OrderItemStoreRequest $request, Order $order): OrderResource
    {
        $order = $this->orderItemService->addOrUpdateItem($order, $request->validated());

        return new OrderResource($order);
    }

    public function destroy(Order $order, int $product): OrderResource
    {
        $order = $this->orderItemService->detachItem($order, $product);

        return new OrderResource($order);
    }
}
