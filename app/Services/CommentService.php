<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\CommentRepositoryInterface;
use App\DTO\Comment\CreateCommentData;
use App\Exceptions\ApiException;
use App\Models\Article;
use Illuminate\Support\Facades\Log;

class CommentService
{
    public function __construct(private CommentRepositoryInterface $repo) {}

    public function listForArticle(int $articleId)
    {
        try { return $this->repo->listApprovedForArticle($articleId); }
        catch (\Throwable $e) { Log::error('comment.list.fail',['article_id'=>$articleId]); throw new ApiException('Yorumlar alınamadı',500); }
    }

    public function create(CreateCommentData|array $data)
    {
        $arr = $data instanceof CreateCommentData ? $data->toArray() : $data;
        $article = Article::findOrFail($arr['article_id']);
        $arr['status'] = 'pending';
        try {
            $c = $this->repo->create($arr)->fresh();
            event(new \App\Events\CommentSubmitted($article, $c));
            return $c;
        } catch (\Throwable $e) { Log::error('comment.create.fail'); throw new ApiException('Yorum kaydedilemedi',500); }
    }

    public function setStatus(int $id, string $status)
    {
        $c = $this->repo->findById($id) ?? throw new ApiException('Yorum bulunamadı',404);
        try {
            $c = $this->repo->update($c, ['status'=>$status])->fresh();
            event(new \App\Events\CommentModerated($c, $status));
            return $c;
        } catch (\Throwable $e) { Log::error('comment.status.fail'); throw new ApiException('Yorum güncellenemedi',500); }
    }

    public function delete(int $id): void
    {
        $c = $this->repo->findById($id) ?? throw new ApiException('Yorum bulunamadı',404);
        $this->repo->delete($c);
    }
}
