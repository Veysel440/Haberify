<?php

namespace App\Http\Controllers;

use App\Services\RssFeedService;
use Illuminate\Http\Response;

class RssController extends Controller
{
    public function __construct(private RssFeedService $svc) {}
    public function __invoke(): Response
    {
        return response($this->svc->build(), 200)->header('Content-Type','application/rss+xml; charset=UTF-8');
    }
}
