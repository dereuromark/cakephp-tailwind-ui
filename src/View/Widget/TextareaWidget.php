<?php
declare(strict_types=1);

namespace TailwindUi\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\Widget\TextareaWidget as CoreTextareaWidget;

class TextareaWidget extends CoreTextareaWidget
{
    use InputGroupTrait;

    public function render(array $data, ContextInterface $context): string
    {
        $data['injectFormControl'] = false;
        $classMap = $this->_loadClassMap();
        $existing = $data['class'] ?? '';
        $data['class'] = trim(($existing ? $existing . ' ' : '') . ($classMap['form.textarea'] ?? ''));
        return $this->_withInputGroup($data, $context);
    }
}
