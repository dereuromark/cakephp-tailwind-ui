<?php

declare(strict_types=1);

namespace TailwindUi\View\Helper;

use TailwindUi\View\PresetLoader;

trait ClassMapTrait
{
    /**
     * @var array<string, string>
     */
    protected array $_classMap = [];

    /**
     * @var array<string, string>
     */
    protected array $_formTemplates = [];

    protected function initClassMap(): void
    {
        if ($this->_classMap) {
            return;
        }

        [$this->_classMap, $this->_formTemplates] = PresetLoader::resolve();
    }

    protected function classMap(string $key): string
    {
        $this->initClassMap();

        return $this->_classMap[$key] ?? '';
    }

    /**
     * Returns the form template overrides contributed by the active preset and
     * any user-supplied overrides. Empty by default (legacy flat presets
     * contribute zero templates).
     *
     * @return array<string, string>
     */
    protected function formTemplates(): array
    {
        $this->initClassMap();

        return $this->_formTemplates;
    }
}
