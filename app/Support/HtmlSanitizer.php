<?php

declare(strict_types=1);

namespace App\Support;

final class HtmlSanitizer
{
    public static function clean(?string $html): ?string
    {
        if ($html === null || $html === '') return $html;

        // HTMLPurifier tercih (composer require ezyang/htmlpurifier)
        if (class_exists(\HTMLPurifier::class)) {
            $config = \HTMLPurifier_Config::createDefault();
            $config->set('Cache.DefinitionImpl', null);
            $config->set('HTML.Doctype', 'HTML5');
            $config->set('URI.DisableJavaScript', true);
            $config->set('Attr.EnableID', false);
            $config->set('HTML.Allowed', implode(',', [
                // blok
                'p','br','hr','blockquote','pre','code','ul','ol','li','h1','h2','h3','h4','h5','h6',
                // inline
                'strong','b','em','i','u','span','a[href|title|rel|target]','small','sub','sup','mark',
                // media
                'img[src|alt|title|width|height]',
                // table
                'table','thead','tbody','tr','th','td',
            ]));
            $config->set('AutoFormat.RemoveEmpty', true);
            $config->set('HTML.SafeImg', true);
            $config->set('URI.SafeIframeRegexp', null); // iframes kapalı

            $purifier = new \HTMLPurifier($config);
            $clean = $purifier->purify($html);
            // dış linklere rel ugc/nofollow
            $clean = preg_replace_callback('#<a\s+[^>]*href="([^"]+)"[^>]*>#i', function($m){
                $href = $m[1];
                $rel = (str_starts_with($href, '/') || str_starts_with($href, (string)config('app.url'))) ? 'noopener' : 'nofollow ugc noopener';
                return preg_replace('#rel="[^"]*"#','', $m[0]).' rel="'.$rel.'"';
            }, $clean);
            return $clean;
        }

        // Basit fallback
        $dom = new \DOMDocument(); @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD);
        // script/style kaldır
        while (($tags = $dom->getElementsByTagName('script'))->length) $tags->item(0)->parentNode->removeChild($tags->item(0));
        while (($tags = $dom->getElementsByTagName('style'))->length)  $tags->item(0)->parentNode->removeChild($tags->item(0));
        // on* attribute ve javascript: kaldır
        $xp = new \DOMXPath($dom);
        foreach ($xp->query('//@*') as $attr) {
            if (str_starts_with(strtolower($attr->nodeName), 'on')) $attr->ownerElement->removeAttributeNode($attr);
            if ($attr->nodeName === 'href' && str_starts_with(strtolower($attr->nodeValue), 'javascript:')) $attr->ownerElement->removeAttributeNode($attr);
        }
        return $dom->saveHTML();
    }
}
