<?php

it('stores comment as pending and sanitized', function () {
    $a = \App\Models\Article::factory()->create();
    $payload = ['body'=>'Nice <script>alert(1)</script> text', 'name'=>'Ali', 'email'=>'a@b.c'];

    $r = $this->postJson("/api/v1/articles/{$a->id}/comments", $payload)->assertCreated();
    $id = $r->json('data.id');

    $c = \App\Models\Comment::find($id);
    expect($c->status)->toBe('pending');
    expect($c->body)->not->toContain('<script>');
});
