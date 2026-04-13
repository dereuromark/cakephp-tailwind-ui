<?php

declare(strict_types=1);

namespace TailwindUi\View\Helper;

use Cake\View\Helper\BreadcrumbsHelper as CoreBreadcrumbsHelper;

class BreadcrumbsHelper extends CoreBreadcrumbsHelper
{
    use OptionsAwareTrait;

    /**
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'ariaCurrent' => 'last',
        'templates' => [
            'wrapper' => '<nav aria-label="breadcrumb"{{attrs}}><ul>{{content}}</ul></nav>',
            'item' => '<li{{attrs}}><a href="{{url}}"{{innerAttrs}}>{{title}}</a></li>{{separator}}',
            'itemWithoutLink' => '<li{{attrs}}><span{{innerAttrs}}>{{title}}</span></li>{{separator}}',
            'separator' => '',
        ],
    ];

    /**
     * {@inheritDoc}
     *
     * Applies Tailwind/DaisyUI class map values to the breadcrumbs wrapper and items.
     */
    public function render(array $attributes = [], array $separator = []): string
    {
        $wrapperClass = $this->classMap('breadcrumbs');

        if ($wrapperClass) {
            $attributes = $this->injectClasses($wrapperClass, $attributes);
        }

        $this->_markActiveCrumb();

        return parent::render($attributes, $separator);
    }

    /**
     * @param array|string $title
     * @param array|string|null $url
     * @param array<string, mixed> $options
     */
    public function add(array|string $title, array|string|null $url = null, array $options = [])
    {
        if (is_array($title)) {
            $crumbs = [];
            foreach ($title as $crumb) {
                $crumbOptions = $this->_injectItemClass($crumb['options'] ?? []);
                $crumbs[] = $crumb + ['title' => '', 'url' => null, 'options' => $crumbOptions];
            }

            return parent::addMany($crumbs);
        }

        return parent::add($title, $url, $this->_injectItemClass($options));
    }

    /**
     * @param array|string $title
     * @param array|string|null $url
     * @param array<string, mixed> $options
     */
    public function prepend(array|string $title, array|string|null $url = null, array $options = [])
    {
        return parent::prepend($title, $url, $this->_injectItemClass($options));
    }

    /**
     * @param array<string, mixed> $options
     */
    public function insertAt(int $index, string $title, array|string|null $url = null, array $options = [])
    {
        return parent::insertAt($index, $title, $url, $this->_injectItemClass($options));
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    protected function _injectItemClass(array $options): array
    {
        $itemClass = $this->classMap('breadcrumbs.item');
        if ($itemClass === '') {
            return $options;
        }

        return $this->injectClasses($itemClass, $options);
    }

    protected function _markActiveCrumb(): void
    {
        if (!$this->crumbs) {
            return;
        }

        $this->_clearActiveCrumb();

        $key = null;
        if ($this->getConfig('ariaCurrent') === 'lastWithLink') {
            foreach (array_reverse($this->crumbs, true) as $candidateKey => $crumb) {
                if (isset($crumb['url'])) {
                    $key = $candidateKey;
                    break;
                }
            }
        } else {
            $key = count($this->crumbs) - 1;
        }

        if ($key === null) {
            return;
        }

        $activeClass = $this->classMap('breadcrumbs.active');
        if ($activeClass !== '') {
            $this->crumbs[$key]['options'] = $this->injectClasses($activeClass, $this->crumbs[$key]['options']);
        }

        if (isset($this->crumbs[$key]['url'])) {
            $this->crumbs[$key]['options']['innerAttrs']['aria-current'] = 'page';
        } else {
            $this->crumbs[$key]['options']['aria-current'] = 'page';
        }
    }

    protected function _clearActiveCrumb(): void
    {
        $activeClass = $this->classMap('breadcrumbs.active');
        foreach ($this->crumbs as $key => $crumb) {
            if ($activeClass !== '') {
                $this->crumbs[$key]['options'] = $this->removeClasses($activeClass, $this->crumbs[$key]['options']);
            }

            unset(
                $this->crumbs[$key]['options']['innerAttrs']['aria-current'],
                $this->crumbs[$key]['options']['aria-current'],
            );
        }
    }
}
