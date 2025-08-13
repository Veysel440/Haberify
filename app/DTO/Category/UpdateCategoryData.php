<?php

declare(strict_types=1);

namespace App\DTO\Category;

final readonly class UpdateCategoryData
{
    public function __construct(public array $payload) {}
    public static function from(array $v): self { return new self($v); }
    public function toArray(): array { return $this->payload; }
}
