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

        $firstOption = $options['first'] ?? true;
        $prevOption = $options['prev'] ?? true;
        $numbersOption = $options['numbers'] ?? true;
        $nextOption = $options['next'] ?? true;
        $lastOption = $options['last'] ?? true;
        unset($options['first'], $options['prev'], $options['numbers'], $options['next'], $options['last']);

        $first = $this->_renderNavLink('first', $firstOption, '«', $options);
        $prev = $this->_renderNavLink('prev', $prevOption, '‹', $options);
        $numbers = $numbersOption === false ? '' : $this->numbers($options);
        $next = $this->_renderNavLink('next', $nextOption, '›', $options);
        $last = $this->_renderNavLink('last', $lastOption, '»', $options);

        return '<div class="' . $paginationClass . '">'
            . $first . $prev . $numbers . $next . $last
            . '</div>';
    }

    /**
     * @param string $method
     * @param mixed $control
     * @param string $defaultText
     * @param array<string, mixed> $options
     */
    protected function _renderNavLink(string $method, mixed $control, string $defaultText, array $options): string
    {
        if ($control === false) {
            return '';
        }

        $text = $defaultText;
        $linkOptions = $options;
        if (is_string($control) || is_int($control)) {
            $text = (string)$control;
        } elseif (is_array($control)) {
            $text = (string)($control['text'] ?? $defaultText);
            $linkOptions = array_merge($options, $control['options'] ?? []);
        }

        return $this->{$method}($text, $linkOptions);
    }
}
