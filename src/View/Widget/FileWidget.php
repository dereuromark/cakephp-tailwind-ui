<?php

declare(strict_types=1);

namespace TailwindUi\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\Widget\FileWidget as CoreFileWidget;

class FileWidget extends CoreFileWidget
{
    use InputGroupTrait;

    /**
     * Note: `prepend`/`append` input-group decoration is **not supported** on
     * file inputs. CakePHP's core FileWidget renders a styled native control
     * that doesn't compose with the daisyUI `join` wrapper. If those keys
     * are passed they're silently dropped — use a regular text input with a
     * hidden file picker via JS if you need an addon next to a file control.
     */
    public function render(array $data, ContextInterface $context): string
    {
        $data['injectFormControl'] = false;
        $classMap = $this->_loadClassMap();
        $existing = $data['class'] ?? '';
        $data['class'] = trim(($existing ? $existing . ' ' : '') . ($classMap['form.file'] ?? ''));
        unset($data['injectFormControl'], $data['prepend'], $data['append']);

        return parent::render($data, $context);
    }
}
