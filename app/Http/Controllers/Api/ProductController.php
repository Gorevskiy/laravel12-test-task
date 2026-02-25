<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService)
    {
    }

    public function index(ProductIndexRequest $request)
    {
        $data = $request->validated();

        $products = Product::query()
            ->with('category')
            ->when(isset($data['category_id']), fn ($query) => $query->where('category_id', $data['category_id']))
            ->latest('id')
            ->paginate($data['per_page'] ?? 10)
            ->withQueryString();

        return ProductResource::collection($products);
    }

    public function store(ProductStoreRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Product $product): ProductResource
    {
        $product->load('category');

        return new ProductResource($product);
    }

    public function update(ProductUpdateRequest $request, Product $product): ProductResource
    {
        $product = $this->productService->update($product, $request->validated());

        return new ProductResource($product);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->productService->delete($product);

        return response()->json(status: 204);
    }
}
