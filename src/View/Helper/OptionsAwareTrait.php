<?php

declare(strict_types=1);

namespace TailwindUi\View\Helper;

use Cake\Core\Configure;

trait OptionsAwareTrait
{
    use ClassMapTrait;

    protected function injectClasses(array|string $classes, array $options): array
    {
        $classes = $this->_toClassArray($classes);
        $existing = $this->_toClassArray($options['class'] ?? null);
        $skip = $this->_toClassArray($options['skip'] ?? null);
        unset($options['skip']);

        foreach ($classes as $class) {
            if (!in_array($class, $existing, true) && !in_array($class, $skip, true)) {
                $existing[] = $class;
            }
        }
        $options['class'] = implode(' ', $existing);

        return $options;
    }

    protected function removeClasses(array|string $classes, array $options): array
    {
        $classes = $this->_toClassArray($classes);
        $existing = $this->_toClassArray($options['class'] ?? null);
        $options['class'] = implode(' ', array_diff($existing, $classes));

        return $options;
    }

    protected function hasAnyClass(array|string $classes, array $options): bool
    {
        $classes = $this->_toClassArray($classes);
        $existing = $this->_toClassArray($options['class'] ?? null);

        return (bool)array_intersect($classes, $existing);
    }

    /**
     * Default color variants. Any of these appearing in the user's class list
     * suppress the per-component default color fallback. Modifiers and sizes
     * (outline, soft, ghost-as-modifier, sm/lg/...) don't suppress it.
     *
     * Apps can extend this list via `Configure::write('TailwindUi.colorVariants', [...])`
     * to promote custom class-map keys (e.g. a project-specific `btn.brand`)
     * into color variants — see `colorVariants()`.
     */
    protected array $colorVariants = [
        'primary',
        'secondary',
        'neutral',
        'accent',
        'success',
        'danger',
        'warning',
        'info',
    ];

    /**
     * Returns the effective color-variant list, including any names added via
     * `Configure::write('TailwindUi.colorVariants', [...])`.
     *
     * @return array<int, string>
     */
    protected function colorVariants(): array
    {
        $extra = Configure::read('TailwindUi.colorVariants');
        if (!is_array($extra) || !$extra) {
            return $this->colorVariants;
        }

        return array_values(array_unique(array_merge($this->colorVariants, $extra)));
    }

    protected function applyButtonClasses(array $data): array
    {
        return $this->applyComponentClasses($data, 'btn', 'primary');
    }

    /**
     * Strip semantic variant/size/modifier keywords from $data['class'] and
     * inject the corresponding class map values for the given component prefix.
     *
     * The set of recognized keywords is derived from the class map: any key
     * starting with `{$prefix}.` is treated as a semantic variant whose tail
     * segment is the keyword to strip. This means apps can add custom keys
     * (e.g. `badge.host`) via `TailwindUi.classMapOverrides` and they are
     * recognized automatically without helper changes.
     *
     * @param array<string, mixed> $data Incoming options/data array.
     * @param string $prefix Class map prefix (e.g. `btn`, `badge`).
     * @param string $default Variant name used when no color variant is set.
     *
     * @return array<string, mixed>
     */
    protected function applyComponentClasses(array $data, string $prefix, string $default): array
    {
        $this->initClassMap();

        $prefixDot = $prefix . '.';
        $variants = [];
        foreach (array_keys($this->_classMap) as $key) {
            if (str_starts_with($key, $prefixDot)) {
                $variants[] = substr($key, strlen($prefixDot));
            }
        }

        $existing = $this->_toClassArray($data['class'] ?? null);
        $colorVariants = $this->colorVariants();
        $hasColor = false;

        foreach ($variants as $variant) {
            if (!in_array($variant, $existing, true)) {
                continue;
            }
            $data = $this->removeClasses($variant, $data);
            $mapped = $this->classMap($prefixDot . $variant);
            if ($mapped !== '') {
                $data = $this->injectClasses($mapped, $data);
            }
            if (in_array($variant, $colorVariants, true)) {
                $hasColor = true;
            }
        }

        $data = $this->injectClasses($this->classMap($prefix), $data);
        if (!$hasColor) {
            $data = $this->injectClasses($this->classMap($prefixDot . $default), $data);
        }

        return $data;
    }

    protected function _toClassArray(mixed $mixed): array
    {
        if (is_array($mixed)) {
            return $mixed;
        }
        if (is_string($mixed) && $mixed !== '') {
            return array_filter(explode(' ', $mixed));
        }

        return [];
    }
}
