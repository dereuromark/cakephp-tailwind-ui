<?php

declare(strict_types=1);

namespace TailwindUi\View\Helper;

use Cake\View\Helper\BreadcrumbsHelper as CoreBreadcrumbsHelper;

class BreadcrumbsHelper extends CoreBreadcrumbsHelper
{
    use OptionsAwareTrait;

    /**
     * {@inheritDoc}
     *
     * Applies Tailwind/DaisyUI class map values to the breadcrumbs wrapper and items.
     */
    public function render(array $attributes = [], array $separator = []): string
    {
        $wrapperClass = $this->classMap('breadcrumbs');
        $itemClass = $this->classMap('breadcrumbs.item');
        $activeClass = $this->classMap('breadcrumbs.active');

        // Inject wrapper class into attributes
        if ($wrapperClass) {
            $existing = $attributes['class'] ?? '';
            $attributes['class'] = trim(($existing ? $existing . ' ' : '') . $wrapperClass);
        }

        // Build item class strings (may be empty for some maps)
        $itemClassAttr = $itemClass ? ' class="' . $itemClass . '"' : '';
        $activeClassFull = trim($itemClass . ' ' . $activeClass);
        $activeClassAttr = $activeClassFull ? ' class="' . $activeClassFull . '"' : '';

        // Override templates to use class map values
        $this->setTemplates([
            'wrapper' => '<nav{{attrs}}><ul>{{content}}</ul></nav>',
            'item' => '<li' . $itemClassAttr . '><a href="{{url}}"{{innerAttrs}}>{{title}}</a></li>{{separator}}',
            'itemWithoutLink' => '<li' . $activeClassAttr . '><span{{innerAttrs}}>{{title}}</span></li>{{separator}}',
            'separator' => '',
        ]);

        return parent::render($attributes, $separator);
    }
}
