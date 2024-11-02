<?php

namespace App\Services;

use DOMDocument;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UrlPreviewService
{
    /**
     * Get preview data for a URL with caching.
     */
    public function getPreview(string $url): array
    {
        // Normalize URL to ensure consistent caching
        $normalizedUrl = $this->normalizeUrl($url);
        
        return Cache::remember(
            'preview_' . md5($normalizedUrl),
            now()->addHour(5), // Increased cache time to 1 hour
            fn () => $this->fetchPreview($normalizedUrl)
        );
    }

    /**
     * Fetch and parse preview data from a URL.
     */
    private function fetchPreview(string $url): array
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(10) // Increased timeout
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                ])
                ->get($url);

            if (!$response->successful()) {
                Log::warning("Failed to fetch URL: {$url}, Status: {$response->status()}");
                return $this->getDefaultPreview($url);
            }

            $html = $response->body();
            
            // Create a new DOMDocument instance
            $doc = new DOMDocument();
            
            // Suppress warnings and load HTML with UTF-8 encoding
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
            @$doc->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);

            $preview = $this->getDefaultPreview($url);

            // Parse meta tags first (OpenGraph takes priority)
            $this->parseMetaTags($doc, $preview, $url);
            
            // If no OG title found, try regular title
            if (empty($preview['title']) || $preview['title'] === $preview['domain']) {
                $this->parseTitle($doc, $preview);
            }

            // Parse favicon
            $this->parseFavicon($doc, $preview, $url);

            // Validate image URL
            if (!empty($preview['image'])) {
                $preview['image'] = $this->validateImageUrl($preview['image']);
            }

            Log::info('Preview Data for URL: ' . $url, $preview);

            return $preview;
        } catch (Exception $e) {
            Log::error("Error fetching preview for URL: {$url}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->getDefaultPreview($url);
        }
    }

    /**
     * Get default preview data structure.
     */
    private function getDefaultPreview(string $url): array
    {
        $domain = parse_url($url, PHP_URL_HOST) ?? '';
        $domain = preg_replace('/^www\./', '', $domain);

        return [
            'title' => $domain,
            'description' => '',
            'image' => '',
            'favicon' => $this->getFallbackFavicon($url),
            'domain' => $domain
        ];
    }

    /**
     * Parse meta tags from DOM document.
     */
    private function parseMetaTags(DOMDocument $doc, array &$preview, string $baseUrl): void
    {
        $metas = $doc->getElementsByTagName('meta');
        
        foreach ($metas as $meta) {
            // OpenGraph tags
            if ($meta->hasAttribute('property')) {
                $this->parseOpenGraphTag(
                    $meta->getAttribute('property'),
                    $meta->getAttribute('content'),
                    $preview,
                    $baseUrl
                );
            }

            // Twitter cards
            if ($meta->hasAttribute('name')) {
                $name = $meta->getAttribute('name');
                $content = $meta->getAttribute('content');

                switch ($name) {
                    case 'twitter:title':
                        if (empty($preview['title'])) {
                            $preview['title'] = $content;
                        }
                        break;
                    case 'twitter:description':
                        if (empty($preview['description'])) {
                            $preview['description'] = $content;
                        }
                        break;
                    case 'twitter:image':
                        if (empty($preview['image'])) {
                            $preview['image'] = $this->resolveUrl($baseUrl, $content);
                        }
                        break;
                    case 'description':
                        if (empty($preview['description'])) {
                            $preview['description'] = $content;
                        }
                        break;
                }
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
            $title = trim($titles->item(0)->nodeValue);
            if (!empty($title)) {
                $preview['title'] = $title;
            }
        }
    }

    /**
     * Parse favicon from DOM document.
     */
    private function parseFavicon(DOMDocument $doc, array &$preview, string $baseUrl): void
    {
        // Check for explicit favicon links
        foreach ($doc->getElementsByTagName('link') as $link) {
            if ($link->hasAttribute('rel')) {
                $rel = strtolower($link->getAttribute('rel'));
                if (in_array($rel, ['icon', 'shortcut icon', 'apple-touch-icon'])) {
                    $href = $link->getAttribute('href');
                    if (!empty($href)) {
                        $preview['favicon'] = $this->resolveUrl($baseUrl, $href);
                        return;
                    }
                }
            }
        }

        // Fallback to default favicon location
        $preview['favicon'] = $this->getFallbackFavicon($baseUrl);
    }

    /**
     * Get fallback favicon URL.
     */
    private function getFallbackFavicon(string $url): string
    {
        $parsedUrl = parse_url($url);
        if (isset($parsedUrl['scheme']) && isset($parsedUrl['host'])) {
            return "{$parsedUrl['scheme']}://{$parsedUrl['host']}/favicon.ico";
        }
        return '';
    }

    /**
     * Normalize URL to ensure consistent caching.
     */
    private function normalizeUrl(string $url): string
    {
        // Add scheme if missing
        if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
            $url = 'https://' . $url;
        }
        
        // Remove trailing slash
        return rtrim($url, '/');
    }

    /**
     * Resolve relative URL to absolute URL.
     */
    private function resolveUrl(string $baseUrl, string $url): string
    {
        if (empty($url)) {
            return '';
        }

        // Check if URL is already absolute
        if (preg_match('~^(?:f|ht)tps?://~i', $url)) {
            return $url;
        }

        // Handle protocol-relative URLs
        if (str_starts_with($url, '//')) {
            $scheme = parse_url($baseUrl, PHP_URL_SCHEME) ?? 'https';
            return $scheme . ':' . $url;
        }

        return rtrim($baseUrl, '/') . '/' . ltrim($url, '/');
    }

    /**
     * Validate and clean image URL.
     */
    private function validateImageUrl(string $url): string
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(5)
                ->head($url);

            if ($response->successful() && 
                str_starts_with($response->header('content-type'), 'image/')) {
                return $url;
            }
        } catch (Exception $e) {
            Log::warning("Failed to validate image URL: {$url}", [
                'error' => $e->getMessage()
            ]);
        }
        return '';
    }
}