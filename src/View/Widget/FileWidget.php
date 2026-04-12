<?php

declare(strict_types=1);

namespace TailwindUi\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\Widget\FileWidget as CoreFileWidget;

class FileWidget extends CoreFileWidget
{
    use InputGroupTrait;

    public function render(array $data, ContextInterface $context): string
    {
        $data['injectFormControl'] = false;
        $classMap = $this->_loadClassMap();
        $existing = $data['class'] ?? '';
        $data['class'] = trim(($existing ? $existing . ' ' : '') . ($classMap['form.file'] ?? ''));
        unset($data['injectFormControl']);

        return parent::render($data, $context);
    }
}
