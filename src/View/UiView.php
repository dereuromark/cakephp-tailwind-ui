<?php
declare(strict_types=1);

namespace TailwindUi\View;

use Cake\View\View;

class UiView extends View
{
    use UiViewTrait;

    public function initialize(): void
    {
        parent::initialize();
        $this->initializeUi();
    }
}
