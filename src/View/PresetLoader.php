<?php

declare(strict_types=1);

namespace TailwindUi\View;

use Cake\Core\Configure;
use Cake\Core\Plugin;

/**
 * Resolves the active class map preset by merging the daisyUI base with the
 * configured preset and any user overrides, accepting either the legacy flat
 * array shape or the extended nested `['classMap' => ..., 'templates' => ...]`
 * shape.
 *
 * Used by both `ClassMapTrait` (helpers) and `InputGroupTrait` (widgets), so
 * the two layers can never drift on how presets are loaded.
 */
final class PresetLoader
{
    /**
     * Resolves the merged class map and form templates for the active preset.
     *
     * @return array{0: array<string, string>, 1: array<string, string>} `[classMap, templates]`
     */
    public static function resolve(): array
    {
        $pluginPath = Plugin::path('TailwindUi');
        [$map, $templates] = self::split(include $pluginPath . 'config/class_maps/daisyui.php');

        $configured = Configure::read('TailwindUi.classMap');
        if (is_string($configured)) {
            $presetFile = $pluginPath . 'config/class_maps/' . $configured . '.php';
            if (file_exists($presetFile)) {
                [$presetMap, $presetTemplates] = self::split(include $presetFile);
                $map = array_merge($map, $presetMap);
                $templates = array_merge($templates, $presetTemplates);
            }
        } elseif (is_array($configured)) {
            [$configMap, $configTemplates] = self::split($configured);
            $map = array_merge($map, $configMap);
            $templates = array_merge($templates, $configTemplates);
        }

        $overrides = Configure::read('TailwindUi.classMapOverrides');
        if (is_array($overrides)) {
            [$overrideMap, $overrideTemplates] = self::split($overrides);
            $map = array_merge($map, $overrideMap);
            $templates = array_merge($templates, $overrideTemplates);
        }

        return [$map, $templates];
    }

    /**
     * Convenience accessor returning just the class map portion.
     *
     * @return array<string, string>
     */
    public static function classMap(): array
    {
        return self::resolve()[0];
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
    public static function split(array $preset): array
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
