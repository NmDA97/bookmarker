<?php

namespace App\Services;

use DOMDocument;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class UrlPreviewService
{
    /**
     * Get preview data for a URL with caching.
     */
    public function getPreview(string $url): array
    {
        return Cache::remember(
            'preview_' . md5($url),
            now()->addHour(),
            fn () => $this->fetchPreview($url)
        );
    }

    /**
     * Fetch and parse preview data from a URL.
     */
    private function fetchPreview(string $url): array
    {
        try {
            $html = Http::withoutVerifying()
                ->timeout(5)
                ->get($url)
                ->body();

            $doc = new DOMDocument();
            @$doc->loadHTML($html, LIBXML_NOERROR);

            $preview = $this->getDefaultPreview($url);

            $this->parseMetaTags($doc, $preview, $url);

            if (empty($preview['title'])) {
                $this->parseTitle($doc, $preview);
            }

            $this->parseFavicon($doc, $preview, $url);

            return $preview;
        } catch (Exception) {
            return $this->getDefaultPreview($url);
        }
    }

    /**
     * Get default preview data structure.
     */
    private function getDefaultPreview(string $url): array
    {
        $domain = parse_url($url, PHP_URL_HOST);

        return [
            'title' => $domain,
            'description' => '',
            'image' => '',
            'favicon' => '',
            'domain' => $domain
        ];
    }

    /**
     * Parse meta tags from DOM document.
     */
    private function parseMetaTags(DOMDocument $doc, array &$preview, string $baseUrl): void
    {
        foreach ($doc->getElementsByTagName('meta') as $meta) {
            if ($meta->hasAttribute('property')) {
                $this->parseOpenGraphTag(
                    $meta->getAttribute('property'),
                    $meta->getAttribute('content'),
                    $preview,
                    $baseUrl
                );
            }

            if ($meta->hasAttribute('name') &&
                $meta->getAttribute('name') === 'description' &&
                empty($preview['description'])) {
                $preview['description'] = $meta->getAttribute('content');
            }
        }
    }

    /**
     * Parse OpenGraph tag data.
     */
    private function parseOpenGraphTag(string $property, string $content, array &$preview, string $baseUrl): void
    {
        switch ($property) {
            case 'og:title':
                $preview['title'] = $content;
                break;
            case 'og:description':
                $preview['description'] = $content;
                break;
            case 'og:image':
                $preview['image'] = $this->resolveUrl($baseUrl, $content);
                break;
        }
    }

    /**
     * Parse title from DOM document.
     */
    private function parseTitle(DOMDocument $doc, array &$preview): void
    {
        $titles = $doc->getElementsByTagName('title');
        if ($titles->length > 0) {
            $preview['title'] = $titles->item(0)->nodeValue;
        }
    }

    /**
     * Parse favicon from DOM document.
     */
    private function parseFavicon(DOMDocument $doc, array &$preview, string $baseUrl): void
    {
        foreach ($doc->getElementsByTagName('link') as $link) {
            if ($link->hasAttribute('rel') &&
                in_array(strtolower($link->getAttribute('rel')), ['icon', 'shortcut icon'])) {
                $preview['favicon'] = $this->resolveUrl($baseUrl, $link->getAttribute('href'));
                break;
            }
        }
    }

    /**
     * Resolve relative URL to absolute URL.
     */
    private function resolveUrl(string $baseUrl, string $url): string
    {
        if (empty($url)) {
            return '';
        }

        if (str_starts_with($url, 'http')) {
            return $url;
        }

        return rtrim($baseUrl, '/') . '/' . ltrim($url, '/');
    }
}
