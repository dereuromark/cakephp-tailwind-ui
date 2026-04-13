<?php

declare(strict_types=1);

namespace TailwindUi\View\Helper;

use Cake\View\Helper\HtmlHelper as CoreHtmlHelper;
use function Cake\Core\h;

class HtmlHelper extends CoreHtmlHelper
{
    use OptionsAwareTrait;

    /**
     * Minimal inline SVG set for the default DaisyUI/Heroicons preset.
     *
     * @var array<string, string>
     */
    protected array $_svgPaths = [
        'check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 12.75l6 6 9-13.5" />',
        'check-circle' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75l2.25 2.25L15 9.75m6 2.25a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'exclamation-triangle' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zm-8.603 3.375h17.206c1.54 0 2.502-1.667 1.732-3L13.732 4.125c-.77-1.333-2.694-1.333-3.464 0L1.665 17.25c-.77 1.333.192 3 1.732 3z" />',
        'information-circle' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.25 11.25h1.5v5.25h-1.5zm0-3.75h1.5v1.5h-1.5z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'pencil' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a2.25 2.25 0 113.182 3.182L10.582 17.13a4.5 4.5 0 01-1.897 1.13L6 19l.74-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />',
        'search' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35m1.35-5.4a6.75 6.75 0 11-13.5 0 6.75 6.75 0 0113.5 0z" />',
    ];

    /**
     * Renders a badge element.
     *
     * @param string $text Text inside the badge.
     * @param array<string, mixed> $options HTML attributes and variant options.
     *
     * @return string Rendered badge HTML.
     */
    public function badge(string $text, array $options = []): string
    {
        $options = $this->applyComponentClasses($options, 'badge', 'secondary');

        $tag = $options['tag'] ?? 'span';
        unset($options['tag']);

        return $this->tag($tag, h($text), $options);
    }

    /**
     * Renders a daisyUI alert element.
     *
     * Mirrors the FlashHelper output for use cases where you want a
     * one-shot inline alert without going through the session — e.g.
     * showing a static notice on a page, or rendering an alert inside
     * a partial that doesn't have access to flash messages.
     *
     * The variant resolves through the same `applyComponentClasses()`
     * machinery used by `badge()` and form buttons, so passing
     * `['class' => 'success']` strips and remaps to `alert-success`.
     * `danger` is accepted as a semantic alias for daisyUI's `error`.
     *
     * @param string $text Alert text. HTML-escaped automatically; pass
     *   `escape => false` in `$options` to render raw HTML.
     * @param array<string, mixed> $options HTML attributes and variant options.
     *   - `class` — variant name (`success`/`error`/`danger`/`warning`/`info`)
     *      or any extra Tailwind classes.
     *   - `tag` — outer element name (default `div`).
     *   - `escape` — whether to escape `$text` (default `true`).
     */
    public function alert(string $text, array $options = []): string
    {
        $escape = $options['escape'] ?? true;
        unset($options['escape']);

        $options = $this->applyComponentClasses($options, 'alert', 'default');

        $tag = $options['tag'] ?? 'div';
        unset($options['tag']);

        $content = $escape ? h($text) : $text;
        if (!isset($options['role'])) {
            $options['role'] = 'alert';
        }

        return $this->tag($tag, $content, $options);
    }

    /**
     * Renders an icon element.
     *
     * @param string $name Icon name.
     * @param array<string, mixed> $options HTML attributes and icon options.
     *
     * @return string Rendered icon HTML.
     */
    public function icon(string $name, array $options = []): string
    {
        $tag = $this->classMap('icon.tag') ?: 'svg';
        $namespace = $this->classMap('icon.namespace');
        $prefix = $this->classMap('icon.prefix');
        $size = $this->classMap('icon.size');
        $sizeOverride = $options['size'] ?? null;
        unset($options['size']);

        if ($tag === 'i') {
            // Font icon (e.g. KTUI)
            $classes = array_filter([$namespace, $prefix, $name]);
            $options = $this->injectClasses(implode(' ', $classes), $options);
            if ($sizeOverride) {
                $options = $this->injectClasses($sizeOverride, $options);
            } elseif ($size) {
                $options = $this->injectClasses($size, $options);
            }

            return $this->tag('i', '', $options);
        }

        // SVG icon (heroicons style)
        $classes = array_filter([$sizeOverride ?: $size]);
        $options = $this->injectClasses(implode(' ', $classes), $options);

        $options += [
            'xmlns' => 'http://www.w3.org/2000/svg',
            'fill' => 'none',
            'viewBox' => '0 0 24 24',
            'stroke' => 'currentColor',
            'aria-hidden' => 'true',
        ];

        $content = $options['content'] ?? $this->_svgPaths[$name] ?? '';
        unset($options['content']);

        if ($content === '') {
            $options['data-icon'] = $name;
        }

        return $this->tag($tag, $content, $options);
    }
}
