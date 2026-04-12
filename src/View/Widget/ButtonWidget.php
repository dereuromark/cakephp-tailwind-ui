<?php

declare(strict_types=1);

namespace TailwindUi\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\Widget\ButtonWidget as CoreButtonWidget;
use TailwindUi\View\Helper\OptionsAwareTrait;

class ButtonWidget extends CoreButtonWidget
{
    use OptionsAwareTrait;

    public function render(array $data, ContextInterface $context): string
    {
        $data = $this->applyButtonClasses($data);

        return parent::render($data, $context);
    }
}
