<?php
declare(strict_types=1);

namespace TailwindUi\View\Helper;

use Cake\View\Helper\FormHelper as CoreFormHelper;
use Cake\View\View;
use function Cake\Core\h;

class FormHelper extends CoreFormHelper {

	use OptionsAwareTrait;

	/**
     * Alignment constant for default (vertical) layout.
     * @var string
     */
	public const ALIGN_DEFAULT = 'default';

	/**
     * Alignment constant for horizontal layout.
     * @var string
     */
	public const ALIGN_HORIZONTAL = 'horizontal';

	/**
     * Current form alignment.
     */
	protected string $_align = self::ALIGN_DEFAULT;

	/**
     * Custom widgets to replace core widgets.
     *
     * @var array<string, mixed>
     */
	protected array $_widgets = [
		'button' => 'TailwindUi\View\Widget\ButtonWidget',
		'datetime' => 'TailwindUi\View\Widget\DateTimeWidget',
		'file' => ['TailwindUi\View\Widget\FileWidget', 'label'],
		'select' => 'TailwindUi\View\Widget\SelectBoxWidget',
		'textarea' => 'TailwindUi\View\Widget\TextareaWidget',
		'_default' => 'TailwindUi\View\Widget\BasicWidget',
	];

	/**
     * Constructor.
     *
     * @param \Cake\View\View $view The View this helper is being attached to.
     * @param array<string, mixed> $config Configuration settings for the helper.
     */
	public function __construct(View $view, array $config = []) {
		// Merge our widgets into config so parent picks them up
		if (!isset($config['widgets'])) {
			$config['widgets'] = $this->_widgets;
		} else {
			$config['widgets'] = $this->_widgets + $config['widgets'];
		}

		// Disable nested checkbox/radio so our custom templates work
		$config['nestedCheckboxAndRadio'] = $config['nestedCheckboxAndRadio'] ?? false;

		parent::__construct($view, $config);

		$this->setTemplates([
			'label' => '<label{{attrs}}>{{text}}</label>',
			'error' => '<div id="{{id}}" class="{{errorClass}}">{{content}}</div>',
			'inputContainer' => '<div class="{{containerClass}}">{{content}}{{help}}</div>',
			'inputContainerError' => '<div class="{{containerClass}}">{{content}}{{error}}{{help}}</div>',
			'checkboxContainer' => '<div class="{{containerClass}}">{{content}}{{help}}</div>',
			'checkboxContainerError' => '<div class="{{containerClass}}">{{content}}{{error}}{{help}}</div>',
			'checkboxFormGroup' => '<div class="flex items-center gap-2 cursor-pointer">{{input}}{{label}}</div>',
			'radioContainer' => '<div class="{{containerClass}}" role="group" aria-labelledby="{{groupId}}">{{content}}{{help}}</div>',
			'radioContainerError' => '<div class="{{containerClass}}" role="group" aria-labelledby="{{groupId}}">{{content}}{{error}}{{help}}</div>',
			'radioWrapper' => '<div class="flex items-center gap-2"><label class="flex items-center gap-2 cursor-pointer">{{hidden}}{{input}}<span class="{{labelClass}}">{{text}}</span></label></div>',
			'radioLabel' => '<label{{attrs}}>{{text}}</label>',
			'multicheckboxContainer' => '<div class="{{containerClass}}" role="group" aria-labelledby="{{groupId}}">{{content}}{{help}}</div>',
			'multicheckboxContainerError' => '<div class="{{containerClass}}" role="group" aria-labelledby="{{groupId}}">{{content}}{{error}}{{help}}</div>',
			'multicheckboxLabel' => '<label{{attrs}}>{{text}}</label>',
			'multicheckboxWrapper' => '<fieldset>{{content}}</fieldset>',
			'multicheckboxTitle' => '<legend class="{{labelClass}} mb-2">{{text}}</legend>',
			'nestingLabel' => '{{hidden}}{{input}}<label{{attrs}}>{{text}}</label>',
			'submitContainer' => '<div class="{{containerClass}}">{{content}}</div>',
		]);
	}

	/**
     * {@inheritDoc}
     *
     * Processes the alignment option and stores it.
     */
	public function create(mixed $context = null, array $options = []): string {
		$this->_align = $options['align'] ?? static::ALIGN_DEFAULT;
		unset($options['align']);

		return parent::create($context, $options);
	}

	/**
     * {@inheritDoc}
     *
     * Resets alignment after form end.
     */
	public function end(array $secureAttributes = []): string {
		$result = parent::end($secureAttributes);
		$this->_align = static::ALIGN_DEFAULT;

		return $result;
	}

	/**
     * {@inheritDoc}
     *
     * Applies Tailwind classes to the control.
     */
	public function control(string $fieldName, array $options = []): string {
		$options += [
			'type' => null,
			'label' => null,
			'error' => null,
			'required' => null,
			'options' => null,
			'templates' => [],
			'templateVars' => [],
			'labelOptions' => true,
			'help' => null,
			'switch' => false,
		];

		$help = $options['help'];
		$isSwitch = $options['switch'];
		unset($options['help'], $options['switch']);

		// Determine container class based on alignment
		if ($this->_align === static::ALIGN_HORIZONTAL) {
			$containerClass = $this->classMap('form.containerHorizontal');
		} else {
			$containerClass = $this->classMap('form.container');
		}

		// Build templates for this control
		$templates = (array)$options['templates'];

		// Determine the type early to apply type-specific classes
		$parsedOptions = $this->_parseOptions($fieldName, $options);
		$type = $parsedOptions['type'];

		// Apply label class
		$labelClass = $this->classMap('form.label');
		if ($this->_align === static::ALIGN_HORIZONTAL) {
			$labelClass = $this->classMap('form.labelHorizontal');
		}

		// Inject label class
		if ($options['label'] !== false) {
			if ($options['label'] === null || is_string($options['label'])) {
				$options['label'] = ['class' => $labelClass, 'text' => $options['label']];
			} elseif (is_array($options['label'])) {
				$options['label']['class'] = trim(($options['label']['class'] ?? '') . ' ' . $labelClass);
			}
		}

		// Apply error class to the field input if field has errors
		$isError = $this->isFieldError($fieldName);
		if ($isError && $type !== 'hidden') {
			$errorKey = match ($type) {
				'select' => 'form.selectError',
				'textarea' => 'form.textareaError',
				default => 'form.inputError',
			};
			$errorClass = $this->classMap($errorKey);
			if ($errorClass) {
				$existing = $options['class'] ?? '';
				$options['class'] = trim(($existing ? $existing . ' ' : '') . $errorClass);
			}
		}

		// Set error class templateVar
		$options['templateVars']['errorClass'] = $this->classMap('form.error');

		// Apply checkbox/switch classes
		if ($type === 'checkbox') {
			if ($isSwitch) {
				$existing = $options['class'] ?? '';
				$options['class'] = trim(($existing ? $existing . ' ' : '') . $this->classMap('form.switch'));
			} else {
				$existing = $options['class'] ?? '';
				$options['class'] = trim(($existing ? $existing . ' ' : '') . $this->classMap('form.checkbox'));
			}
			$options['templateVars']['labelClass'] = $this->classMap('form.label');
		}

		// Apply radio classes
		if ($type === 'radio') {
			$existing = $options['class'] ?? '';
			$options['class'] = trim(($existing ? $existing . ' ' : '') . $this->classMap('form.radio'));
			$options['templateVars']['labelClass'] = $this->classMap('form.label');
			$options['templateVars']['groupId'] = $this->_domId($fieldName) . '-label';
		}

		// Apply multicheckbox label class
		if (
			$type === 'multicheckbox'
			|| ($type === 'select' && isset($options['multiple']) && $options['multiple'] === 'checkbox')
		) {
			$options['templateVars']['labelClass'] = $this->classMap('form.label');
			$options['templateVars']['groupId'] = $this->_domId($fieldName) . '-label';
		}

		// Apply range class
		if ($type === 'range') {
			$existing = $options['class'] ?? '';
			$options['class'] = trim(($existing ? $existing . ' ' : '') . $this->classMap('form.range'));
		}

		// Handle help text
		$helpHtml = '';
		if ($help !== null) {
			$helpClass = $this->classMap('form.helpText');
			$helpId = $this->_domId($fieldName) . '-help';
			$helpHtml = '<div id="' . $helpId . '" class="' . $helpClass . '">' . h($help) . '</div>';

			$describedBy = $options['aria-describedby'] ?? '';
			$options['aria-describedby'] = trim(($describedBy ? $describedBy . ' ' : '') . $helpId);
		}

		$options['templateVars']['help'] = $helpHtml;
		$options['templateVars']['containerClass'] = $containerClass;

		// Override container templates to use templateVars-based containerClass
		$containerTemplates = [
			'inputContainer' => '<div class="{{containerClass}}">{{content}}{{help}}</div>',
			'inputContainerError' => '<div class="{{containerClass}}">{{content}}{{error}}{{help}}</div>',
			'checkboxContainer' => '<div class="{{containerClass}}">{{content}}{{help}}</div>',
			'checkboxContainerError' => '<div class="{{containerClass}}">{{content}}{{error}}{{help}}</div>',
			'radioContainer' => '<div class="{{containerClass}}" role="group" aria-labelledby="{{groupId}}">{{content}}{{help}}</div>',
			'radioContainerError' => '<div class="{{containerClass}}" role="group" aria-labelledby="{{groupId}}">{{content}}{{error}}{{help}}</div>',
			'multicheckboxContainer' => '<div class="{{containerClass}}" role="group" aria-labelledby="{{groupId}}">{{content}}{{help}}</div>',
			'multicheckboxContainerError' => '<div class="{{containerClass}}" role="group" aria-labelledby="{{groupId}}">{{content}}{{error}}{{help}}</div>',
			'submitContainer' => '<div class="{{containerClass}}">{{content}}</div>',
			'error' => '<div id="{{id}}" class="{{errorClass}}">{{content}}</div>',
			'multicheckboxTitle' => '<legend class="{{labelClass}} mb-2">{{text}}</legend>',
		];

		$options['templates'] = array_merge($containerTemplates, $templates);

		return parent::control($fieldName, $options);
	}

	/**
     * {@inheritDoc}
     *
     * Applies button classes via applyButtonClasses().
     */
	public function submit(?string $caption = null, array $options = []): string {
		$containerClass = $this->classMap('form.container');
		if (!isset($options['templateVars'])) {
			$options['templateVars'] = [];
		}
		$options['templateVars']['containerClass'] = $containerClass;

		// Apply button classes to the submit input
		$options = $this->applyButtonClasses($options);

		return parent::submit($caption, $options);
	}

	/**
     * Override _inputContainerTemplate to apply containerClass from templateVars.
     */
	protected function _inputContainerTemplate(array $options): string {
		$inputContainerTemplate = $options['options']['type'] . 'Container' . $options['errorSuffix'];
		if (!$this->templater()->get($inputContainerTemplate)) {
			$inputContainerTemplate = 'inputContainer' . $options['errorSuffix'];
		}

		$templateVars = $options['options']['templateVars'] ?? [];
		$containerClass = $templateVars['containerClass'] ?? $this->classMap('form.container');

		return $this->formatTemplate($inputContainerTemplate, [
			'content' => $options['content'],
			'error' => $options['error'],
			'label' => $options['label'] ?? '',
			'required' => $options['options']['required'] ? ' ' . $this->templater()->get('requiredClass') : '',
			'type' => $options['options']['type'],
			'containerClass' => $containerClass,
			'templateVars' => $templateVars,
			'help' => $templateVars['help'] ?? '',
			'groupId' => $templateVars['groupId'] ?? '',
			'errorClass' => $templateVars['errorClass'] ?? $this->classMap('form.error'),
		]);
	}

	/**
     * Override _groupTemplate to pass labelClass.
     */
	protected function _groupTemplate(array $options): string {
		$groupTemplate = $options['options']['type'] . 'FormGroup';
		if (!$this->templater()->get($groupTemplate)) {
			$groupTemplate = 'formGroup';
		}

		$templateVars = $options['options']['templateVars'] ?? [];

		return $this->formatTemplate($groupTemplate, [
			'input' => $options['input'] ?? [],
			'label' => $options['label'],
			'error' => $options['error'],
			'templateVars' => $templateVars,
			'labelClass' => $templateVars['labelClass'] ?? $this->classMap('form.label'),
			'text' => $templateVars['labelText'] ?? '',
		]);
	}

}
