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

    /**
     * Render a DaisyUI-style tooltip wrapper around a label.
     *
     * Outputs `<span class="tooltip" data-tip="..."><label content/></span>`. The
     * data-tip is rendered escaped by default; pass `escape => false` if you
     * intentionally need raw HTML in the tip (rare). The trigger content is
     * inserted verbatim — pass an already-escaped string or call `h()` yourself.
     *
     * @param string $content Trigger markup (already escaped).
     * @param string $tip Tooltip text.
     * @param array<string, mixed> $options HTML attributes for the wrapper plus
     *  optional `position` (top|bottom|left|right) and `escape` (default true).
     *
     * @return string
     */
    public function tooltip(string $content, string $tip, array $options = []): string
    {
        $escape = $options['escape'] ?? true;
        $position = $options['position'] ?? null;
        unset($options['escape'], $options['position']);

        $base = $this->classMap('tooltip') ?: 'tooltip';
        $positionClass = $position !== null ? ($this->classMap('tooltip.' . $position) ?: 'tooltip-' . $position) : null;
        $classes = array_filter([$base, $positionClass]);
        $options = $this->injectClasses(implode(' ', $classes), $options);

        $options['data-tip'] = $escape ? h($tip) : $tip;

        return $this->tag('span', $content, $options + ['escape' => false]);
    }

    /**
     * Render a `<dialog>`-based modal pair: a trigger button plus the modal markup.
     *
     * DaisyUI modals are native HTML5 `<dialog>` elements opened by the trigger
     * button via a small inline `onclick` — except we keep CSP-friendly markup
     * by using a `popovertarget`-style `data-modal-target` attribute pair that
     * the bundled JS hook can wire up. Callers that prefer a different opening
     * mechanism can disable the trigger via `trigger => false` and open the
     * dialog themselves.
     *
     * @param string $triggerText Visible text on the trigger button.
     * @param string $bodyHtml Pre-rendered HTML for the modal body.
     * @param array<string, mixed> $options
     *  - `id` (string): id for the dialog (auto-generated if missing).
     *  - `title` (string|null): optional title rendered above $bodyHtml.
     *  - `triggerClass` (string|null): class override for the trigger button.
     *  - `dialogClass` (string|null): class override for the dialog.
     *  - `trigger` (bool): emit the trigger button (default true).
     *
     * @return string
     */
    public function modal(string $triggerText, string $bodyHtml, array $options = []): string
    {
        $id = $options['id'] ?? 'modal-' . substr(bin2hex(random_bytes(4)), 0, 8);
        $title = $options['title'] ?? null;
        $triggerClass = $options['triggerClass'] ?? ($this->classMap('modal.trigger') ?: 'btn');
        $dialogClass = $options['dialogClass'] ?? ($this->classMap('modal') ?: 'modal');
        $boxClass = $this->classMap('modal.box') ?: 'modal-box';
        $emitTrigger = $options['trigger'] ?? true;

        $trigger = '';
        if ($emitTrigger) {
            $trigger = $this->tag(
                'button',
                h($triggerText),
                [
                    'type' => 'button',
                    'class' => $triggerClass,
                    'data-tailwind-ui-modal-open' => $id,
                    'escape' => false,
                ],
            );
        }

        $heading = $title !== null ? '<h3 class="font-bold text-lg">' . h($title) . '</h3>' : '';
        $closeBtn = '<form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" aria-label="Close">✕</button></form>';

        $dialog = '<dialog id="' . h($id) . '" class="' . h($dialogClass) . '">'
            . '<div class="' . h($boxClass) . '">' . $closeBtn . $heading . $bodyHtml . '</div>'
            . '</dialog>';

        return $trigger . $dialog;
    }

    /**
     * Render a popover pair using the HTML5 popover API: a trigger plus the popover.
     *
     * Falls back gracefully on browsers without the popover API (the trigger and
     * popover content are both visible in the DOM; the popover just doesn't auto-open
     * without explicit JS).
     *
     * @param string $triggerText Visible text on the trigger button.
     * @param string $bodyHtml Pre-rendered HTML for the popover body.
     * @param array<string, mixed> $options
     *  - `id` (string): id for the popover (auto-generated if missing).
     *  - `triggerClass` (string|null): class override for the trigger button.
     *  - `popoverClass` (string|null): class override for the popover element.
     *
     * @return string
     */
    public function popover(string $triggerText, string $bodyHtml, array $options = []): string
    {
        $id = $options['id'] ?? 'popover-' . substr(bin2hex(random_bytes(4)), 0, 8);
        $triggerClass = $options['triggerClass'] ?? ($this->classMap('popover.trigger') ?: 'btn');
        $popoverClass = $options['popoverClass'] ?? ($this->classMap('popover') ?: 'popover card bg-base-100 shadow p-4');

        $trigger = '<button type="button" popovertarget="' . h($id) . '" class="' . h($triggerClass) . '">' . h($triggerText) . '</button>';
        $body = '<div id="' . h($id) . '" popover class="' . h($popoverClass) . '">' . $bodyHtml . '</div>';

        return $trigger . $body;
    }
}
