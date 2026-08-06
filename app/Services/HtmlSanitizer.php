<?php

namespace App\Services;

use DOMComment;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

final class HtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'a', 'b', 'blockquote', 'br', 'caption', 'div', 'em', 'h1',
        'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'i', 'li', 'ol', 'p',
        's', 'span', 'strong', 'table', 'tbody', 'td', 'tfoot', 'th',
        'thead', 'tr', 'u', 'ul',
    ];

    private const DROP_WITH_CONTENT = [
        'applet', 'audio', 'base', 'button', 'canvas', 'embed', 'form',
        'iframe', 'input', 'link', 'math', 'meta', 'object', 'script',
        'select', 'source', 'style', 'svg', 'textarea', 'video',
    ];

    private const STYLE_PROPERTIES = [
        'background-color', 'border', 'border-bottom', 'border-collapse',
        'border-color', 'border-left', 'border-right', 'border-style',
        'border-top', 'border-width', 'color', 'font-style', 'font-weight',
        'padding', 'text-align', 'text-decoration', 'vertical-align', 'width',
    ];

    public function sanitize(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        if (! class_exists(DOMDocument::class)) {
            return $this->sanitizeWithoutDom($html);
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previousState = libxml_use_internal_errors(true);

        $document->loadHTML(
            '<?xml encoding="utf-8" ?><div data-sanitizer-root="1">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previousState);

        $root = (new DOMXPath($document))
            ->query('//*[@data-sanitizer-root="1"]')
            ?->item(0);

        if (! $root instanceof DOMElement) {
            return '';
        }

        foreach (iterator_to_array($root->childNodes) as $child) {
            $this->cleanNode($child);
        }

        $output = '';

        foreach ($root->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }

        return trim($output);
    }

    private function cleanNode(DOMNode $node): void
    {
        if ($node instanceof DOMComment) {
            $node->parentNode?->removeChild($node);
            return;
        }

        if (! $node instanceof DOMElement) {
            return;
        }

        $tag = strtolower($node->tagName);

        if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
            $node->parentNode?->removeChild($node);
            return;
        }

        foreach (iterator_to_array($node->childNodes) as $child) {
            $this->cleanNode($child);
        }

        if (! in_array($tag, self::ALLOWED_TAGS, true)) {
            $parent = $node->parentNode;

            if ($parent) {
                while ($node->firstChild) {
                    $parent->insertBefore($node->firstChild, $node);
                }

                $parent->removeChild($node);
            }

            return;
        }

        $this->cleanAttributes($node, $tag);
    }

    private function cleanAttributes(DOMElement $element, string $tag): void
    {
        foreach (iterator_to_array($element->attributes) as $attribute) {
            $name = strtolower($attribute->name);
            $value = trim($attribute->value);

            if ($name === 'style' && $this->supportsStyle($tag)) {
                $style = $this->sanitizeStyle($value);

                if ($style !== '') {
                    $element->setAttribute('style', $style);
                    continue;
                }
            }

            if (in_array($name, ['colspan', 'rowspan'], true)
                && in_array($tag, ['td', 'th'], true)
                && preg_match('/^\d{1,2}$/', $value)
                && (int) $value > 0) {
                continue;
            }

            if ($tag === 'a' && $name === 'href' && $this->isSafeUrl($value)) {
                continue;
            }

            if ($tag === 'a' && $name === 'title') {
                continue;
            }

            if ($tag === 'a' && $name === 'target' && $value === '_blank') {
                $element->setAttribute('rel', 'noopener noreferrer');
                continue;
            }

            if ($tag === 'a' && $name === 'rel') {
                $element->setAttribute('rel', 'noopener noreferrer');
                continue;
            }

            $element->removeAttribute($attribute->name);
        }
    }

    private function supportsStyle(string $tag): bool
    {
        return in_array($tag, [
            'div', 'p', 'span', 'table', 'thead', 'tbody', 'tfoot',
            'tr', 'td', 'th',
        ], true);
    }

    private function sanitizeStyle(string $style): string
    {
        if (preg_match('/url\s*\(|expression\s*\(|@import|behavior\s*:|-moz-binding/i', $style)) {
            return '';
        }

        $safeDeclarations = [];

        foreach (explode(';', $style) as $declaration) {
            if (! str_contains($declaration, ':')) {
                continue;
            }

            [$property, $value] = array_map('trim', explode(':', $declaration, 2));
            $property = strtolower($property);

            if (! in_array($property, self::STYLE_PROPERTIES, true)) {
                continue;
            }

            if ($value === '' || ! preg_match('/^[#(),.%\w\s-]+$/u', $value)) {
                continue;
            }

            $safeDeclarations[] = $property.': '.$value;
        }

        return implode('; ', $safeDeclarations);
    }

    private function isSafeUrl(string $url): bool
    {
        if ($url === '' || str_starts_with($url, '#') || str_starts_with($url, '/')) {
            return true;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https', 'mailto', 'tel'], true);
    }

    private function sanitizeWithoutDom(string $html): string
    {
        $html = preg_replace(
            '#<(script|style|iframe|object|embed|svg|math|form)[^>]*>.*?</\1>#is',
            '',
            $html
        ) ?? '';

        $allowed = '<'.implode('><', self::ALLOWED_TAGS).'>';
        $html = strip_tags($html, $allowed);

        return preg_replace('/<([a-z0-9]+)\s+[^>]*>/i', '<$1>', $html) ?? '';
    }
}
