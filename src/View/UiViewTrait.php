<?php

declare(strict_types=1);

namespace TailwindUi\View;

trait UiViewTrait
{
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
            $this->setLayout('TailwindUi.default');
        } elseif (is_string($layout)) {
            $this->setLayout($layout);
        }
    }
}
