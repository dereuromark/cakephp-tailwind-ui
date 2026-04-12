<?php
declare(strict_types=1);

namespace TailwindUi\View\Helper;

use Cake\View\Helper\HtmlHelper as CoreHtmlHelper;
use function Cake\Core\h;

class HtmlHelper extends CoreHtmlHelper {

	use OptionsAwareTrait;

	/**
     * Renders a badge element.
     *
     * @param string $text Text inside the badge.
     * @param array<string, mixed> $options HTML attributes and variant options.
     * @return string Rendered badge HTML.
     */
	public function badge(string $text, array $options = []): string {
		$variants = ['primary', 'secondary', 'success', 'danger', 'warning', 'info'];
		$sizes = ['sm', 'lg'];

		$existing = $this->_toClassArray($options['class'] ?? null);
		$hasVariant = false;

		foreach ($variants as $variant) {
			if (in_array($variant, $existing, true)) {
				$hasVariant = true;
				$options = $this->removeClasses($variant, $options);
				$options = $this->injectClasses($this->classMap('badge.' . $variant), $options);
			}
		}

		if (in_array('outline', $existing, true)) {
			$options = $this->removeClasses('outline', $options);
			$options = $this->injectClasses($this->classMap('badge.outline'), $options);
		}

		foreach ($sizes as $size) {
			if (in_array($size, $existing, true)) {
				$options = $this->removeClasses($size, $options);
				$sizeClass = $this->classMap('badge.' . $size);
				if ($sizeClass) {
					$options = $this->injectClasses($sizeClass, $options);
				}
			}
		}

		$options = $this->injectClasses($this->classMap('badge'), $options);

		if (!$hasVariant) {
			$options = $this->injectClasses($this->classMap('badge.secondary'), $options);
		}

		$tag = $options['tag'] ?? 'span';
		unset($options['tag']);

		return $this->tag($tag, h($text), $options);
	}

	/**
     * Renders an icon element.
     *
     * @param string $name Icon name.
     * @param array<string, mixed> $options HTML attributes and icon options.
     * @return string Rendered icon HTML.
     */
	public function icon(string $name, array $options = []): string {
		$tag = $this->classMap('icon.tag') ?: 'svg';
		$namespace = $this->classMap('icon.namespace');
		$prefix = $this->classMap('icon.prefix');
		$size = $this->classMap('icon.size');

		if ($tag === 'i') {
			// Font icon (e.g. KTUI)
			$classes = array_filter([$namespace, $prefix, $name]);
			$options = $this->injectClasses(implode(' ', $classes), $options);
			if ($size) {
				$options = $this->injectClasses($size, $options);
			}

			return $this->tag('i', '', $options);
		}

		// SVG icon (heroicons style)
		$classes = array_filter([$size]);
		$options = $this->injectClasses(implode(' ', $classes), $options);

		$options += [
			'xmlns' => 'http://www.w3.org/2000/svg',
			'fill' => 'none',
			'viewBox' => '0 0 24 24',
			'stroke' => 'currentColor',
			'aria-hidden' => 'true',
		];

		return $this->tag($tag, '', $options);
	}

}
