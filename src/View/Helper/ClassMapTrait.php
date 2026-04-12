<?php
declare(strict_types=1);

namespace TailwindUi\View\Helper;

use Cake\Core\Configure;
use Cake\Core\Plugin;

trait ClassMapTrait {

	protected array $_classMap = [];

	protected function initClassMap(): void {
		if ($this->_classMap) {
			return;
		}

		$pluginPath = Plugin::path('TailwindUi');
		$base = include $pluginPath . 'config/class_maps/daisyui.php';

		$configured = Configure::read('TailwindUi.classMap');
		if (is_string($configured)) {
			$presetFile = $pluginPath . 'config/class_maps/' . $configured . '.php';
			if (file_exists($presetFile)) {
				$preset = include $presetFile;
				$base = array_merge($base, $preset);
			}
		} elseif (is_array($configured)) {
			$base = array_merge($base, $configured);
		}

		$overrides = Configure::read('TailwindUi.classMapOverrides');
		if (is_array($overrides)) {
			$base = array_merge($base, $overrides);
		}

		$this->_classMap = $base;
	}

	protected function classMap(string $key): string {
		$this->initClassMap();

		return $this->_classMap[$key] ?? '';
	}

}
