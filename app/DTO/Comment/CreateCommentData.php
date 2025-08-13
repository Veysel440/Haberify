<?php
declare(strict_types=1);

namespace App\DTO\Comment;

final readonly class CreateCommentData
{
    public function __construct(
        public int $article_id,
        public ?int $user_id,
        public string $body,
        public ?string $name = null,
        public ?string $email = null,
        public string $status = 'pending',
        public ?string $ip = null,
        public ?string $ua = null,
    ) {}

    public static function from(array $v): self
    {
        return new self(
            article_id: (int)$v['article_id'],
            user_id: isset($v['user_id']) ? (int)$v['user_id'] : null,
            body: (string)$v['body'],
            name: $v['name'] ?? null,
            email: $v['email'] ?? null,
            status: (string)($v['status'] ?? 'pending'),
            ip: $v['ip'] ?? null,
            ua: $v['ua'] ?? null,
        );
    }
    public function toArray(): array { return get_object_vars($this); }
}
