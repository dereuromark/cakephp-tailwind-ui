<?php
declare(strict_types=1);

namespace TailwindUi\View\Helper;

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

    protected function applyButtonClasses(array $data): array
    {
        $variants = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'outline'];
        $existing = $this->_toClassArray($data['class'] ?? null);
        $hasVariant = false;

        foreach ($variants as $variant) {
            if (in_array($variant, $existing, true)) {
                $hasVariant = true;
                $data = $this->removeClasses($variant, $data);
                $data = $this->injectClasses($this->classMap('btn.' . $variant), $data);
            }
        }

        $sizes = ['sm', 'lg'];
        foreach ($sizes as $size) {
            if (in_array($size, $existing, true)) {
                $data = $this->removeClasses($size, $data);
                $sizeClass = $this->classMap('btn.' . $size);
                if ($sizeClass) {
                    $data = $this->injectClasses($sizeClass, $data);
                }
            }
        }

        $data = $this->injectClasses($this->classMap('btn'), $data);
        if (!$hasVariant) {
            $data = $this->injectClasses($this->classMap('btn.primary'), $data);
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
