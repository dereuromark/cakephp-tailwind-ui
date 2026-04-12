<?php
declare(strict_types=1);

namespace TailwindUi\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\Widget\BasicWidget as CoreBasicWidget;

class BasicWidget extends CoreBasicWidget
{
    use InputGroupTrait;

    public function render(array $data, ContextInterface $context): string
    {
        return $this->_withInputGroup($data, $context);
    }
}
