<?php

declare(strict_types=1);

namespace TailwindUi\View\Widget;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\View\Form\ContextInterface;

trait InputGroupTrait
{
    protected function _withInputGroup(array $data, ContextInterface $context): string
    {
        $prepend = $data['prepend'] ?? null;
        $append = $data['append'] ?? null;
        $injectFormControl = $data['injectFormControl'] ?? true;
        unset($data['prepend'], $data['append'], $data['injectFormControl']);

        $type = $data['type'] ?? 'text';
        if ($injectFormControl && $type !== 'hidden') {
            $classMap = $this->_loadClassMap();
            $inputClass = $classMap['form.input'] ?? '';
            $existing = $data['class'] ?? '';
            $data['class'] = trim(($existing ? $existing . ' ' : '') . $inputClass);
        }

        $result = parent::render($data, $context);

        if ($prepend === null && $append === null) {
            return $result;
        }

        $classMap = $this->_loadClassMap();
        $containerClass = $classMap['form.inputGroupContainer'] ?? 'join w-full';
        $textClass = $classMap['form.inputGroupText'] ?? '';

        $prependHtml = $prepend !== null ? $this->_renderAddon($prepend, $textClass) : '';
        $appendHtml = $append !== null ? $this->_renderAddon($append, $textClass) : '';

        return '<div class="' . $containerClass . '">' . $prependHtml . $result . $appendHtml . '</div>';
    }

    protected function _renderAddon(array|string $addon, string $textClass): string
    {
        if (is_string($addon)) {
            if ($this->_isButton($addon)) {
                return $addon;
            }

            return '<span class="' . $textClass . '">' . $addon . '</span>';
        }
        $html = '';
        foreach ($addon as $item) {
            $html .= $this->_renderAddon($item, $textClass);
        }

        return $html;
    }

    protected function _isButton(string $html): bool
    {
        return str_contains($html, '<button') || str_contains($html, 'type="submit"');
    }

    protected function _loadClassMap(): array
    {
        $pluginPath = Plugin::path('TailwindUi');
        $base = $this->_extractClassMap(include $pluginPath . 'config/class_maps/daisyui.php');
        $configured = Configure::read('TailwindUi.classMap');
        if (is_string($configured)) {
            $presetFile = $pluginPath . 'config/class_maps/' . $configured . '.php';
            if (file_exists($presetFile)) {
                $base = array_merge($base, $this->_extractClassMap(include $presetFile));
            }
        } elseif (is_array($configured)) {
            $base = array_merge($base, $this->_extractClassMap($configured));
        }
        $overrides = Configure::read('TailwindUi.classMapOverrides');
        if (is_array($overrides)) {
            $base = array_merge($base, $this->_extractClassMap($overrides));
        }

        return $base;
    }

    /**
     * Accepts either the legacy flat class-map array or the extended nested
     * shape `['classMap' => [...], 'templates' => [...]]` and returns just the
     * class map portion.
     *
     * @param array<string, mixed> $preset
     *
     * @return array<string, string>
     */
    protected function _extractClassMap(array $preset): array
    {
        if (isset($preset['classMap']) && is_array($preset['classMap'])) {
            /** @var array<string, string> $map */
            $map = $preset['classMap'];

            return $map;
        }

        /** @var array<string, string> $preset */
        return $preset;
    }
}
