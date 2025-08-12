<?php

namespace App\Exports;

use App\Models\Article;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ArticlesExport implements FromQuery, WithHeadings
{
    public function query()
    {
        return Article::query()->select('id','title','slug','status','published_at','language');
    }

    public function headings(): array
    {
        return ['id','title','slug','status','published_at','language'];
    }
}
