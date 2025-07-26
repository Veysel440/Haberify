<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;

class CategoryService implements CategoryServiceInterface
{
    public function list(): LengthAwarePaginator
    {
        return Category::latest()->paginate(10);
    }

    public function find(int $id): ?Category
    {
        return Category::findOrFail($id);
    }

    public function create(array $data): Category
    {
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['image'] = $data['image']->store('categories', 'public');
        }
        return Category::create($data);
    }

    public function update(int $id, array $data): Category
    {
        $category = Category::findOrFail($id);
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            if ($category->image) {
                \Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $data['image']->store('categories', 'public');
        }
        $category->update($data);
        return $category;
    }

    public function delete(int $id): bool
    {
        $category = Category::findOrFail($id);


        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();
    }
}
