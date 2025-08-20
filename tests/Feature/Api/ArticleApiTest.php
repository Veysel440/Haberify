<?php

it('lists only published articles', function () {
    \App\Models\Article::factory()->count(2)->create();
    \App\Models\Article::factory()->draft()->create();

    $res = $this->getJson('/api/v1/articles')->assertOk();
    $data = $res->json('data.data');
    expect(collect($data)->every(fn($a)=>$a['status']==='published'))->toBeTrue();
});

it('creates article with permission', function () {
    $user = userWithPermissions(['articles.create']);
    $cat  = \App\Models\Category::factory()->create();

    $payload = [
        'title'=>'Test Title','category_id'=>$cat->id,'summary'=>'sum','body'=>'<p>ok</p>',
        'status'=>'draft','language'=>'tr','tag_ids'=>[],
    ];
    $this->actingAs($user)->postJson('/api/v1/articles', $payload)
        ->assertCreated()
        ->assertJsonPath('data.title','Test Title');
});
