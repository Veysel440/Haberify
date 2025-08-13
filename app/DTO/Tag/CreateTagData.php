<?php

declare(strict_types=1);

namespace App\DTO\Tag;

final readonly class CreateTagData
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description = null,
        public bool $is_active = true
    ) {}

    public static function from(array $v): self
    {
        return new self(
            name: (string)$v['name'],
            slug: (string)($v['slug'] ?? \Str::slug($v['name'])),
            description: $v['description'] ?? null,
            is_active: (bool)($v['is_active'] ?? true),
        );
    }
    public function toArray(): array { return get_object_vars($this); }
}
