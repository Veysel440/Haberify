<?php

namespace App\Services\Admin;

use App\Models\Article;
use Illuminate\Support\Facades\DB;

class ArticleBulkService
{
    public function handle(array $ids, string $action): array
    {
        $affected = 0;
        DB::transaction(function() use ($ids, $action, &$affected) {
            $q = Article::whereIn('id',$ids);
            switch ($action) {
                case 'publish':
                    $affected = $q->update(['status'=>'published','published_at'=>now()]);
                    Article::whereIn('id',$ids)->get()->each->searchable();
                    break;
                case 'unpublish':
                    $affected = $q->update(['status'=>'draft','published_at'=>null]);
                    Article::whereIn('id',$ids)->get()->each->unsearchable();
                    break;
                case 'feature':
                    $affected = $q->update(['is_featured'=>true]);
                    break;
                case 'unfeature':
                    $affected = $q->update(['is_featured'=>false]);
                    break;
                case 'delete':
                    $affected = $q->delete();
                    break;
            }
        });
        return ['action'=>$action,'affected'=>$affected];
    }
}
