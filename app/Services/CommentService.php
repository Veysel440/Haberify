<?php

namespace App\Services;


use App\Contracts\CommentRepositoryInterface;
use App\Events\{CommentSubmitted, CommentModerated};
class CommentService
{
    public function __construct(private CommentRepositoryInterface $repo) {}

    public function listForArticle(int $articleId)
    { return $this->repo->listApprovedForArticle($articleId); }

    public function create(int $articleId, array $d)
    {
        $article = \App\Models\Article::findOrFail($articleId);
        $d['article_id'] = $articleId;
        $d['status'] = 'pending';
        $d['ip'] = request()->ip();
        $d['ua'] = request()->header('User-Agent');

        $c = $this->repo->create($d)->fresh();
        event(new CommentSubmitted($article, $c));
        return $c;
    }

    public function setStatus(int $id, string $status)
    {
        $c = $this->repo->findById($id) ?? abort(404);
        $c = $this->repo->update($c, ['status'=>$status])->fresh();
        event(new CommentModerated($c, $status));
        return $c;
    }

    public function delete(int $id)
    { $c = $this->repo->findById($id) ?? abort(404); $this->repo->delete($c); }
}
