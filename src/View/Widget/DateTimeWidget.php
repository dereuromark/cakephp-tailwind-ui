<?php
declare(strict_types=1);

namespace TailwindUi\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\Widget\DateTimeWidget as CoreDateTimeWidget;

class DateTimeWidget extends CoreDateTimeWidget
{
    use InputGroupTrait;

    public function render(array $data, ContextInterface $context): string
    {
        return $this->_withInputGroup($data, $context);
    }
}
