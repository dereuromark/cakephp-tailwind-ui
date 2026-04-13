<?php

declare(strict_types=1);

namespace TailwindUi\View\Helper;

use Cake\Core\Configure;
use Cake\Core\Plugin;

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

        $pluginPath = Plugin::path('TailwindUi');
        [$baseMap, $baseTemplates] = $this->_splitPreset(include $pluginPath . 'config/class_maps/daisyui.php');

        $configured = Configure::read('TailwindUi.classMap');
        if (is_string($configured)) {
            $presetFile = $pluginPath . 'config/class_maps/' . $configured . '.php';
            if (file_exists($presetFile)) {
                [$presetMap, $presetTemplates] = $this->_splitPreset(include $presetFile);
                $baseMap = array_merge($baseMap, $presetMap);
                $baseTemplates = array_merge($baseTemplates, $presetTemplates);
            }
        } elseif (is_array($configured)) {
            [$configMap, $configTemplates] = $this->_splitPreset($configured);
            $baseMap = array_merge($baseMap, $configMap);
            $baseTemplates = array_merge($baseTemplates, $configTemplates);
        }

        $overrides = Configure::read('TailwindUi.classMapOverrides');
        if (is_array($overrides)) {
            [$overrideMap, $overrideTemplates] = $this->_splitPreset($overrides);
            $baseMap = array_merge($baseMap, $overrideMap);
            $baseTemplates = array_merge($baseTemplates, $overrideTemplates);
        }

        $this->_classMap = $baseMap;
        $this->_formTemplates = $baseTemplates;
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

    /**
     * Accepts either the legacy flat class-map array or the extended nested
     * shape `['classMap' => [...], 'templates' => [...]]` and returns a
     * `[classMap, templates]` tuple.
     *
     * @param array<string, mixed> $preset
     *
     * @return array{0: array<string, string>, 1: array<string, string>}
     */
    protected function _splitPreset(array $preset): array
    {
        if (isset($preset['classMap']) && is_array($preset['classMap'])) {
            /** @var array<string, string> $map */
            $map = $preset['classMap'];
            /** @var array<string, string> $templates */
            $templates = isset($preset['templates']) && is_array($preset['templates'])
                ? $preset['templates']
                : [];

            return [$map, $templates];
        }

        /** @var array<string, string> $preset */
        return [$preset, []];
    }
}
