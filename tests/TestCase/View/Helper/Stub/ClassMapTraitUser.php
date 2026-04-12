<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper\Stub;

use Cake\View\View;
use TailwindUi\View\Helper\ClassMapTrait;

class ClassMapTraitUser
{
    use ClassMapTrait;

    public function __construct(protected View $view)
    {
    }

    public function get(string $key): string
    {
        return $this->classMap($key);
    }

    public function reset(): void
    {
        $this->_classMap = [];
    }
}
