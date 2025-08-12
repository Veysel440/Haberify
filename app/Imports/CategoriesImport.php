<?php

namespace App\Imports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;

class CategoriesImport implements ToModel
{
    public function model(array $row)
    {
        if ($row[0] === 'name') return null;
        return Category::firstOrCreate(
            ['slug'=>$row[1]],
            ['name'=>$row[0], 'description'=>$row[2] ?? null, 'is_active'=>true]
        );
    }
}
