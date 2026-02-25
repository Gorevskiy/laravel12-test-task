<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderIndexRequest;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService)
    {
    }

    public function index(OrderIndexRequest $request)
    {
        $data = $request->validated();

        $orders = Order::query()
            ->with(['products', 'user'])
            ->when(isset($data['user_id']), fn ($query) => $query->where('user_id', $data['user_id']))
            ->latest('id')
            ->paginate($data['per_page'] ?? 10)
            ->withQueryString();

        return OrderResource::collection($orders);
    }

    public function store(OrderStoreRequest $request): JsonResponse
    {
        $order = $this->orderService->create($request->validated());

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Order $order): OrderResource
    {
        $order->load(['products', 'user']);

        return new OrderResource($order);
    }

    public function update(OrderUpdateRequest $request, Order $order): OrderResource
    {
        $order = $this->orderService->updateStatus($order, $request->validated());

        return new OrderResource($order);
    }

    public function destroy(Order $order): JsonResponse
    {
        $this->orderService->delete($order);

        return response()->json(status: 204);
    }
}
