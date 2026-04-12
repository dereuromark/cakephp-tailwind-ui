<?php

declare(strict_types=1);

namespace TailwindUi\View\Helper;

use Cake\View\Helper\PaginatorHelper as CorePaginatorHelper;
use Cake\View\View;

class PaginatorHelper extends CorePaginatorHelper
{
    use ClassMapTrait;

    /**
     * Constructor.
     *
     * @param \Cake\View\View $view The View this helper is being attached to.
     * @param array<string, mixed> $config Configuration settings for the helper.
     */
    public function __construct(View $view, array $config = [])
    {
        parent::__construct($view, $config);
    }

    /**
     * Renders a full pagination block wrapped in a container div.
     *
     * @param array<string, mixed> $options Options for pagination.
     *
     * @return string HTML pagination block.
     */
    public function links(array $options = []): string
    {
        $paginationClass = $this->classMap('pagination');
        $itemClass = $this->classMap('pagination.item');
        $activeClass = $this->classMap('pagination.active');
        $disabledClass = $this->classMap('pagination.disabled');

        $this->setTemplates([
            'nextActive' => '<a class="' . $itemClass . '" rel="next"{{attrs}}>{{text}}</a>',
            'nextDisabled' => '<span class="' . $itemClass . ' ' . $disabledClass . '" aria-disabled="true">{{text}}</span>',
            'prevActive' => '<a class="' . $itemClass . '" rel="prev"{{attrs}}>{{text}}</a>',
            'prevDisabled' => '<span class="' . $itemClass . ' ' . $disabledClass . '" aria-disabled="true">{{text}}</span>',
            'current' => '<span class="' . $itemClass . ' ' . $activeClass . '" aria-current="page">{{text}}</span>',
            'number' => '<a class="' . $itemClass . '"{{attrs}}>{{text}}</a>',
            'first' => '<a class="' . $itemClass . '"{{attrs}}>{{text}}</a>',
            'last' => '<a class="' . $itemClass . '"{{attrs}}>{{text}}</a>',
            'ellipsis' => '<span class="' . $itemClass . ' ' . $disabledClass . '">…</span>',
        ]);

        $first = $this->first('«', $options);
        $prev = $this->prev('‹', $options);
        $numbers = $this->numbers($options);
        $next = $this->next('›', $options);
        $last = $this->last('»', $options);

        return '<div class="' . $paginationClass . '">'
            . $first . $prev . $numbers . $next . $last
            . '</div>';
    }
}
