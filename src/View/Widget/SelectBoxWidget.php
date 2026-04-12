<?php

declare(strict_types=1);

namespace TailwindUi\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\Widget\SelectBoxWidget as CoreSelectBoxWidget;

class SelectBoxWidget extends CoreSelectBoxWidget
{
    use InputGroupTrait;

    public function render(array $data, ContextInterface $context): string
    {
        $data['injectFormControl'] = false;
        $classMap = $this->_loadClassMap();
        $existing = $data['class'] ?? '';
        $data['class'] = trim(($existing ? $existing . ' ' : '') . ($classMap['form.select'] ?? ''));
        $prepend = $data['prepend'] ?? null;
        $append = $data['append'] ?? null;
        unset($data['prepend'], $data['append'], $data['injectFormControl']);
        $result = parent::render($data, $context);
        if ($prepend === null && $append === null) {
            return $result;
        }
        $containerClass = $classMap['form.inputGroupContainer'] ?? 'join w-full';
        $textClass = $classMap['form.inputGroupText'] ?? '';
        $prependHtml = $prepend !== null ? $this->_renderAddon($prepend, $textClass) : '';
        $appendHtml = $append !== null ? $this->_renderAddon($append, $textClass) : '';

        return '<div class="' . $containerClass . '">' . $prependHtml . $result . $appendHtml . '</div>';
    }
}
