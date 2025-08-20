<?php


use App\Support\HtmlSanitizer;

it('removes scripts and dangerous attrs', function () {
    $dirty = '<p onclick="x()">x</p><img src=x onerror=alert(1)><script>evil()</script><a href="javascript:alert(1)">j</a>';
    $clean = HtmlSanitizer::clean($dirty);
    expect($clean)->not->toContain('<script>')
        ->not->toContain('onerror')
        ->not->toContain('onclick')
        ->not->toContain('javascript:');
});
