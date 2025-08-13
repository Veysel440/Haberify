<?php
declare(strict_types=1);

namespace App\DTO\Article;

final readonly class CreateArticleData
{
    public function __construct(
        public int $author_id,
        public int $category_id,
        public string $title,
        public string $slug,
        public ?string $summary,
        public ?string $body,
        public string $status = 'draft',
        public ?string $language = 'tr',
        /** @var int[]|null */
        public ?array $tag_ids = null,
    ) {}

    public static function from(array $v): self
    {
        return new self(
            author_id: (int) $v['author_id'],
            category_id: (int) $v['category_id'],
            title: (string) $v['title'],
            slug: (string) ($v['slug'] ?? \Str::slug($v['title'])),
            summary: $v['summary'] ?? null,
            body: $v['body'] ?? null,
            status: (string) ($v['status'] ?? 'draft'),
            language: $v['language'] ?? 'tr',
            tag_ids: $v['tag_ids'] ?? null,
        );
    }

    public function toArray(): array { return get_object_vars($this); }
}
