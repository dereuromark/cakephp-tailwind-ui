<?php

declare(strict_types=1);

namespace TailwindUi\View\Helper;

use Cake\View\Helper\FormHelper as CoreFormHelper;
use Cake\View\View;
use function Cake\Core\h;

class FormHelper extends CoreFormHelper
{
    use OptionsAwareTrait;

    /**
     * Alignment constant for default (vertical) layout.
     *
     * @var string
     */
    public const ALIGN_DEFAULT = 'default';

    /**
     * Alignment constant for horizontal layout.
     *
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
     * Plugin default (div-based) form templates. Used when the active preset
     * does not supply its own templates block, and always used when the form
     * is in horizontal alignment mode.
     *
     * @var array<string, string>
     */
    protected array $_defaultFormTemplates = [
        'label' => '<label{{attrs}}>{{text}}</label>',
        'error' => '<div id="{{id}}" class="{{errorClass}}">{{content}}</div>',
        'inputContainer' => '<div class="{{containerClass}}">{{content}}{{help}}</div>',
        'inputContainerError' => '<div class="{{containerClass}}">{{content}}{{error}}{{help}}</div>',
        'checkboxContainer' => '<div class="{{containerClass}}">{{content}}{{help}}</div>',
        'checkboxContainerError' => '<div class="{{containerClass}}">{{content}}{{error}}{{help}}</div>',
        'checkboxFormGroup' => '{{label}}',
        'checkboxWrapper' => '<div class="{{wrapperClass}}">{{label}}</div>',
        'radioContainer' => '<div class="{{containerClass}}" role="group" aria-labelledby="{{groupId}}">{{content}}{{help}}</div>',
        'radioContainerError' => '<div class="{{containerClass}}" role="group" aria-labelledby="{{groupId}}">{{content}}{{error}}{{help}}</div>',
        'radioWrapper' => '<div class="{{wrapperClass}}">{{label}}</div>',
        'radioLabel' => '<label{{attrs}}>{{text}}</label>',
        'multicheckboxContainer' => '<div class="{{containerClass}}" role="group" aria-labelledby="{{groupId}}">{{content}}{{help}}</div>',
        'multicheckboxContainerError' => '<div class="{{containerClass}}" role="group" aria-labelledby="{{groupId}}">{{content}}{{error}}{{help}}</div>',
        'multicheckboxLabel' => '<label{{attrs}}>{{text}}</label>',
        'multicheckboxWrapper' => '<fieldset>{{content}}</fieldset>',
        'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}} {{text}}</label>',
        'submitContainer' => '<div class="{{containerClass}}">{{content}}</div>',
        'inputHelp' => '<div class="{{helperClass}}">{{text}}</div>',
    ];

    /**
     * Constructor.
     *
     * @param \Cake\View\View $view The View this helper is being attached to.
     * @param array<string, mixed> $config Configuration settings for the helper.
     */
    public function __construct(View $view, array $config = [])
    {
        if (!isset($config['widgets'])) {
            $config['widgets'] = $this->_widgets;
        } else {
            $config['widgets'] = $this->_widgets + $config['widgets'];
        }

        parent::__construct($view, $config);

        $this->setTemplates($this->_defaultFormTemplates);
    }

    /**
     * {@inheritDoc}
     *
     * Processes the alignment option and stores it.
     */
    public function create(mixed $context = null, array $options = []): string
    {
        $this->_align = $options['align'] ?? static::ALIGN_DEFAULT;
        unset($options['align']);

        return parent::create($context, $options);
    }

    /**
     * {@inheritDoc}
     *
     * Resets alignment after form end.
     */
    public function end(array $secureAttributes = []): string
    {
        $result = parent::end($secureAttributes);
        $this->_align = static::ALIGN_DEFAULT;

        return $result;
    }

    /**
     * {@inheritDoc}
     *
     * Applies Tailwind classes and the fieldset (or div) container markup.
     */
    public function control(string $fieldName, array $options = []): string
    {
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
        $floating = (bool)($options['floating'] ?? false);
        unset($options['help'], $options['switch'], $options['floating']);

        $parsedOptions = $this->_parseOptions($fieldName, $options);
        $type = $parsedOptions['type'];

        $size = $options['size'] ?? null;
        unset($options['size']);

        $isHorizontal = $this->_align === static::ALIGN_HORIZONTAL;
        $isSingleCheckbox = $type === 'checkbox';
        $isGroupInput = $type === 'radio' || $type === 'multicheckbox'
            || ($type === 'select' && ($options['multiple'] ?? null) === 'checkbox');

        $controlTemplates = $this->_buildControlTemplates($isHorizontal);

        // Resolve label class (legend in fieldset mode, label otherwise).
        if ($options['label'] !== false) {
            $labelClass = $this->_resolveLabelClass($type, $isHorizontal, $isGroupInput, $isSingleCheckbox);
            if ($options['label'] === null || is_string($options['label'])) {
                $options['label'] = ['class' => $labelClass, 'text' => $options['label']];
            } elseif (is_array($options['label'])) {
                $options['label']['class'] = trim(($options['label']['class'] ?? '') . ' ' . $labelClass);
            }
        }

        // Apply widget classes.
        if ($type === 'checkbox') {
            $mapKey = $isSwitch ? 'form.switch' : 'form.checkbox';
            $options = $this->injectClasses($this->classMap($mapKey), $options);
            $options['templateVars']['labelClass'] = $this->classMap('form.label');
            $options['templateVars']['wrapperClass'] = $this->classMap('form.checkboxLabelWrapper');
        } elseif ($type === 'radio') {
            $options = $this->injectClasses($this->classMap('form.radio'), $options);
            $options['templateVars']['labelClass'] = $this->classMap('form.label');
            $options['templateVars']['wrapperClass'] = $this->classMap('form.checkboxLabelWrapper');
            $options['templateVars']['groupId'] = $this->_domId($fieldName) . '-label';
        } elseif ($type === 'multicheckbox' || ($type === 'select' && ($options['multiple'] ?? null) === 'checkbox')) {
            $options['templateVars']['labelClass'] = $this->classMap('form.label');
            $options['templateVars']['groupId'] = $this->_domId($fieldName) . '-label';
        } elseif ($type === 'range') {
            $options = $this->injectClasses($this->classMap('form.range'), $options);
        }

        // Apply size modifier (input/select/textarea).
        if ($size !== null && in_array($type, ['text', 'email', 'password', 'url', 'tel', 'search', 'number', 'select', 'textarea'], true)) {
            $sizeKey = match ($type) {
                'select' => 'form.select.' . $size,
                'textarea' => 'form.textarea.' . $size,
                default => 'form.input.' . $size,
            };
            $sizeClass = $this->classMap($sizeKey);
            if ($sizeClass !== '') {
                $options = $this->injectClasses($sizeClass, $options);
            }
        }

        // Apply validator/error class to the input if the field has errors.
        $isError = $this->isFieldError($fieldName);
        if ($isError && $type !== 'hidden') {
            $errorKey = match ($type) {
                'select' => 'form.selectError',
                'textarea' => 'form.textareaError',
                default => 'form.inputError',
            };
            $errorClass = $this->classMap($errorKey);
            if ($errorClass) {
                $options = $this->injectClasses($errorClass, $options);
            }
        }

        // Build help fragment (wraps with the preset's inputHelp template).
        $helpHtml = '';
        if ($help !== null) {
            $helperClass = $this->classMap('form.helpText');
            $helpId = $this->_domId($fieldName) . '-help';
            $helpTpl = $controlTemplates['inputHelp'] ?? '<div class="{{helperClass}}">{{text}}</div>';
            $helpHtml = strtr($helpTpl, [
                '{{helperClass}}' => $helperClass,
                '{{text}}' => h($help),
            ]);
            // Inject the id via a wrapper attribute if the template uses <div> or <p>.
            $helpHtml = preg_replace('/^(<(?:div|p))\b/', '$1 id="' . $helpId . '"', $helpHtml) ?? $helpHtml;

            $describedBy = $options['aria-describedby'] ?? '';
            $options['aria-describedby'] = trim(($describedBy ? $describedBy . ' ' : '') . $helpId);
        }

        // Stash template vars.
        $containerClass = $isHorizontal
            ? $this->classMap('form.containerHorizontal')
            : $this->classMap('form.container');
        $fieldsetClass = $this->classMap('form.fieldset');
        if ($fieldsetClass === '') {
            $fieldsetClass = $containerClass;
        }

        $options['templateVars']['help'] = $helpHtml;
        $options['templateVars']['containerClass'] = $containerClass;
        $options['templateVars']['fieldsetClass'] = $fieldsetClass;
        $options['templateVars']['errorClass'] = $this->classMap('form.error');

        // Floating label mode: replace the fieldset/legend structure with a
        // daisyUI `<label class="floating-label">` wrapping a <span> + input.
        // Only valid for text-style inputs and selects/textareas.
        if ($floating && in_array($type, ['text', 'email', 'password', 'url', 'tel', 'search', 'number', 'select', 'textarea'], true)) {
            $floatingClass = $this->classMap('form.floatingLabel');
            if ($floatingClass !== '') {
                // daisyUI's floating-label needs a placeholder set on the input.
                if (!isset($options['placeholder'])) {
                    $options['placeholder'] = ' ';
                }
                $controlTemplates['label'] = '<span{{attrs}}>{{text}}</span>';
                $controlTemplates['formGroup'] = '<label class="' . $floatingClass . '">{{label}}{{input}}</label>';
                $controlTemplates['inputContainer'] = '<div class="{{containerClass}}">{{content}}{{help}}</div>';
                $controlTemplates['inputContainerError'] = '<div class="{{containerClass}}">{{content}}{{error}}{{help}}</div>';
                // Strip the legend class — the label is now an inline span.
                if (is_array($options['label'] ?? null)) {
                    $options['label']['class'] = '';
                }
            }
        }

        // Merge our control templates on top of user-supplied template overrides.
        $userTemplates = (array)$options['templates'];
        $options['templates'] = array_merge($controlTemplates, $userTemplates);

        return parent::control($fieldName, $options);
    }

    /**
     * {@inheritDoc}
     *
     * Applies button classes via applyButtonClasses().
     */
    public function submit(?string $caption = null, array $options = []): string
    {
        if (!isset($options['templateVars'])) {
            $options['templateVars'] = [];
        }
        $options['templateVars']['containerClass'] = $this->classMap('form.container');

        $options = $this->applyButtonClasses($options);

        // Ensure submit uses the plugin default submitContainer template, not
        // whatever fieldset override the preset registered.
        $options['templates'] = array_merge(
            ['submitContainer' => $this->_defaultFormTemplates['submitContainer']],
            (array)($options['templates'] ?? []),
        );

        return parent::submit($caption, $options);
    }

    /**
     * Override _inputContainerTemplate to apply containerClass from templateVars.
     *
     * @param array<string, mixed> $options
     */
    protected function _inputContainerTemplate(array $options): string
    {
        $inputContainerTemplate = $options['options']['type'] . 'Container' . $options['errorSuffix'];
        if (!$this->templater()->get($inputContainerTemplate)) {
            $inputContainerTemplate = 'inputContainer' . $options['errorSuffix'];
        }

        $templateVars = $options['options']['templateVars'] ?? [];
        $containerClass = $templateVars['containerClass'] ?? $this->classMap('form.container');
        $fieldsetClass = $templateVars['fieldsetClass'] ?? $containerClass;

        return $this->formatTemplate($inputContainerTemplate, [
            'content' => $options['content'],
            'error' => $options['error'],
            'label' => $options['label'] ?? '',
            'required' => $options['options']['required'] ? ' ' . $this->templater()->get('requiredClass') : '',
            'type' => $options['options']['type'],
            'containerClass' => $containerClass,
            'fieldsetClass' => $fieldsetClass,
            'templateVars' => $templateVars,
            'help' => $templateVars['help'] ?? '',
            'groupId' => $templateVars['groupId'] ?? '',
            'errorClass' => $templateVars['errorClass'] ?? $this->classMap('form.error'),
        ]);
    }

    /**
     * Override _groupTemplate to pass labelClass.
     *
     * @param array<string, mixed> $options
     */
    protected function _groupTemplate(array $options): string
    {
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

    /**
     * Returns the merged control template set. When the form is in horizontal
     * alignment mode, preset template overrides are ignored and the div-based
     * plugin defaults are used. Otherwise, preset overrides layer on top.
     *
     * @return array<string, string>
     */
    protected function _buildControlTemplates(bool $isHorizontal): array
    {
        if ($isHorizontal) {
            return $this->_defaultFormTemplates;
        }

        return array_merge($this->_defaultFormTemplates, $this->formTemplates());
    }

    /**
     * Returns the label class for a given control type and layout mode.
     * Resolves the tension between "label above input" (legend in fieldset mode),
     * horizontal label, and the inline-flex single-checkbox label.
     */
    protected function _resolveLabelClass(
        string $type,
        bool $isHorizontal,
        bool $isGroupInput,
        bool $isSingleCheckbox,
    ): string {
        if ($isSingleCheckbox) {
            return 'inline-flex items-center gap-2 cursor-pointer';
        }

        if ($isHorizontal) {
            return $this->classMap('form.labelHorizontal');
        }

        $legendClass = $this->classMap('form.fieldsetLegend');
        if ($legendClass !== '') {
            return $legendClass;
        }

        $labelClass = $this->classMap('form.label');
        if ($isGroupInput) {
            $labelClass = trim($labelClass . ' block mb-2');
        }

        return $labelClass;
    }
}
