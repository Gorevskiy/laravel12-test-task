<?php

namespace App\Services;

use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use App\Models\Product;
use Throwable;

class ProductService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Product
    {
        /** @var Product $product */
        $product = Product::query()->create($data);
        $product->load('category');

        $this->dispatchSafely(new ProductCreated($product));

        return $product;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        $product->load('category');

        $this->dispatchSafely(new ProductUpdated($product));

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
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
