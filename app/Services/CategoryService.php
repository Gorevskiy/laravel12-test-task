<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Category
    {
        /** @var Category $category */
        $category = Category::query()->create($data);

        return $category;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category;
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
