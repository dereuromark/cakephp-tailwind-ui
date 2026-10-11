<?php

declare(strict_types=1);

namespace TailwindUi\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\Widget\BasicWidget as CoreFileWidget;

class FileWidget extends CoreFileWidget
{
    use InputGroupTrait;

    /**
     * Note: `prepend`/`append` input-group decoration is **not supported** on
     * file inputs. The widget renders a native file control
     * that doesn't compose with the daisyUI `join` wrapper. If those keys
     * are passed they're dropped. Use a regular text input with a
     * hidden file picker via JS if you need an addon next to a file control.
     */
    public function render(array $data, ContextInterface $context): string
    {
        $data['type'] = 'file';
        unset($data['val']);
        $data['injectFormControl'] = false;
        $classMap = $this->_loadClassMap();
        $existing = $data['class'] ?? '';
        $data['class'] = trim(($existing ? $existing . ' ' : '') . ($classMap['form.file'] ?? ''));
        unset($data['injectFormControl'], $data['prepend'], $data['append']);

        return parent::render($data, $context);
    }
}
