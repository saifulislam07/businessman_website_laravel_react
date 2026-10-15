<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Turns text saved from the admin's rich-text editor into HTML that is safe to publish.
 *
 * Only a small allow-list survives: paragraphs, line breaks, bold/italic/strike, lists,
 * headings, quotes and links (http, https, mailto, tel). Scripts, styles, classes, inline
 * styles, images and event handlers are removed. Sanitizing happens on the way out, so
 * nothing stored in the database can reach visitors unfiltered.
 */
class RichText
{
    private static ?HtmlSanitizer $sanitizer = null;

    /**
     * Safe HTML for the public site, or null when empty. Plain text saved before the editor
     * existed (blank-line separated paragraphs) is converted to paragraphs.
     */
    public static function html(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if ($value === strip_tags($value)) {
            $html = collect(preg_split('/\R\s*\R/', trim($value)))
                ->map(fn (string $paragraph) => '<p>'.nl2br(e(trim($paragraph)), false).'</p>')
                ->implode('');
        } else {
            $html = self::sanitizer()->sanitize($value);
        }

        return self::text($html) === '' ? null : $html;
    }

    /** Plain text with all markup removed (for table cells, meta descriptions, etc.). */
    public static function text(?string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags(str_replace(['</p>', '<br>', '<br/>', '</li>'], ' ', (string) $value)), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }

    private static function sanitizer(): HtmlSanitizer
    {
        return self::$sanitizer ??= new HtmlSanitizer(
            (new HtmlSanitizerConfig)
                ->allowElement('p')
                ->allowElement('br')
                ->allowElement('strong')
                ->allowElement('b')
                ->allowElement('em')
                ->allowElement('i')
                ->allowElement('del')
                ->allowElement('ul')
                ->allowElement('ol')
                ->allowElement('li')
                ->allowElement('h2')
                ->allowElement('h3')
                ->allowElement('h4')
                ->allowElement('blockquote')
                ->allowElement('a', ['href'])
                ->allowLinkSchemes(['http', 'https', 'mailto', 'tel'])
                ->allowRelativeLinks(false)
                ->forceAttribute('a', 'rel', 'noopener noreferrer')
                ->withMaxInputLength(100000),
        );
    }
}
