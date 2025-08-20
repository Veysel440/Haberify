<?php

it('denies update without permission and allows with permission', function () {
    $a = \App\Models\Article::factory()->create();
    $uNo = \App\Models\User::factory()->create();
    $this->actingAs($uNo)->putJson("/api/v1/articles/{$a->id}", ['title'=>'x'])->assertForbidden();

    $uYes = userWithPermissions(['articles.update']);
    $this->actingAs($uYes)->putJson("/api/v1/articles/{$a->id}", ['title'=>'x'])->assertOk();
});
