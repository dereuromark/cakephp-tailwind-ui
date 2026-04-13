<?php

declare(strict_types=1);

namespace TailwindUi\View\Helper;

use Cake\View\Helper\HtmlHelper as CoreHtmlHelper;
use function Cake\Core\h;

class HtmlHelper extends CoreHtmlHelper
{
    use OptionsAwareTrait;

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

        if ($tag === 'i') {
            // Font icon (e.g. KTUI)
            $classes = array_filter([$namespace, $prefix, $name]);
            $options = $this->injectClasses(implode(' ', $classes), $options);
            if ($size) {
                $options = $this->injectClasses($size, $options);
            }

            return $this->tag('i', '', $options);
        }

        // SVG icon (heroicons style)
        $classes = array_filter([$size]);
        $options = $this->injectClasses(implode(' ', $classes), $options);

        $options += [
            'xmlns' => 'http://www.w3.org/2000/svg',
            'fill' => 'none',
            'viewBox' => '0 0 24 24',
            'stroke' => 'currentColor',
            'aria-hidden' => 'true',
        ];

        return $this->tag($tag, '', $options);
    }
}
