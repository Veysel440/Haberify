<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;

class SitemapController extends Controller
{
    public function __construct(private SitemapService $svc) {}
    public function __invoke()
    {
        return response($this->svc->build(),200)->header('Content-Type','application/xml; charset=UTF-8');
    }
}
