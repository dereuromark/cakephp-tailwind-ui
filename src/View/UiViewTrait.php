<?php

declare(strict_types=1);

namespace TailwindUi\View;

use Cake\Core\Configure;

trait UiViewTrait
{
    /**
     * Register plugin helpers and (optionally) set a preset-appropriate layout.
     *
     * Accepted $options keys:
     * - layout: true (default) to set a preset-appropriate layout, false to
     *   keep the current layout, or a string to set a specific layout name.
     *
     * @param array<string, mixed> $options
     */
    public function initializeUi(array $options = []): void
    {
        $helpers = [
            'Html' => ['className' => 'TailwindUi.Html'],
            'Form' => ['className' => 'TailwindUi.Form'],
            'Flash' => ['className' => 'TailwindUi.Flash'],
            'Paginator' => ['className' => 'TailwindUi.Paginator'],
            'Breadcrumbs' => ['className' => 'TailwindUi.Breadcrumbs'],
        ];
        foreach ($helpers as $name => $config) {
            $this->helpers()->load($name, $config);
        }
        $layout = $options['layout'] ?? true;
        if ($layout === true) {
            $preset = Configure::read('TailwindUi.classMap');
            $layoutName = $preset === 'ktui' ? 'TailwindUi.ktui' : 'TailwindUi.default';
            $this->setLayout($layoutName);
        } elseif (is_string($layout)) {
            $this->setLayout($layout);
        }
    }
}
