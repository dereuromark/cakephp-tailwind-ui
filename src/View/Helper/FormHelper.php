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
     * Alignment constant for inline (single-row) layout. Used for search
     * and filter bars. Labels render as screen-reader-only, help text is
     * suppressed, and all controls flow in one flex row.
     *
     * @var string
     */
    public const ALIGN_INLINE = 'inline';

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

        $output = parent::create($context, $options);

        if ($this->_align === static::ALIGN_INLINE) {
            $wrapperClass = $this->classMap('form.inlineWrapper');
            $output .= '<div class="' . $wrapperClass . '">';
        }

        return $output;
    }

    /**
     * {@inheritDoc}
     *
     * Resets alignment after form end.
     */
    public function end(array $secureAttributes = []): string
    {
        $prefix = '';
        if ($this->_align === static::ALIGN_INLINE) {
            $prefix = '</div>';
        }

        $result = $prefix . parent::end($secureAttributes);
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
            'tooltip' => null,
            'feedbackStyle' => null,
        ];

        $help = $options['help'];
        $isSwitch = $options['switch'];
        $tooltip = $options['tooltip'];
        $feedbackStyle = $options['feedbackStyle'];
        $floating = (bool)($options['floating'] ?? false);
        unset(
            $options['help'],
            $options['switch'],
            $options['tooltip'],
            $options['feedbackStyle'],
            $options['floating'],
        );

        $parsedOptions = $this->_parseOptions($fieldName, $options);
        $type = $parsedOptions['type'];

        $size = $options['size'] ?? null;
        $color = $options['color'] ?? null;
        unset($options['size'], $options['color']);

        $isHorizontal = $this->_align === static::ALIGN_HORIZONTAL;
        $isInline = $this->_align === static::ALIGN_INLINE;
        $isSingleCheckbox = $type === 'checkbox';
        $isGroupInput = $type === 'radio' || $type === 'multicheckbox'
            || ($type === 'select' && ($options['multiple'] ?? null) === 'checkbox');

        $controlTemplates = $this->_buildControlTemplates($isHorizontal || $isInline);

        // Resolve label class (legend in fieldset mode, sr-only in inline, label otherwise).
        if ($options['label'] !== false) {
            if ($isInline) {
                $labelClass = $this->classMap('form.labelInline');
            } else {
                $labelClass = $this->_resolveLabelClass($type, $isHorizontal, $isGroupInput, $isSingleCheckbox);
            }
            if ($options['label'] === null || is_string($options['label'])) {
                $options['label'] = ['class' => $labelClass, 'text' => $options['label']];
            } elseif (is_array($options['label'])) {
                $options['label']['class'] = trim(($options['label']['class'] ?? '') . ' ' . $labelClass);
            }

            // Append tooltip icon to the label text if requested.
            if ($tooltip !== null) {
                $currentText = $options['label']['text'] ?? null;
                if ($currentText === null) {
                    $currentText = h($this->_inflect($fieldName));
                } elseif (is_string($currentText)) {
                    $currentText = h($currentText);
                }
                $options['label']['text'] = $currentText . $this->_renderLabelTooltip($tooltip);
                $options['label']['escape'] = false;
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
            $options = $this->injectClasses($this->classMap('form.checkbox'), $options);
            $options['templateVars']['labelClass'] = $this->classMap('form.label');
            $options['templateVars']['groupId'] = $this->_domId($fieldName) . '-label';
        } elseif ($type === 'range') {
            $options = $this->injectClasses($this->classMap('form.range'), $options);
        }

        // Apply size modifier across all widget families that have a size
        // variant block in the class map. Unmapped combos are silent no-ops.
        if ($size !== null) {
            $sizePrefix = $this->_sizePrefix($type, $isSwitch);
            if ($sizePrefix !== null) {
                $sizeClass = $this->classMap($sizePrefix . '.' . $size);
                if ($sizeClass !== '') {
                    $options = $this->injectClasses($sizeClass, $options);
                }
            }
        }

        // Apply color modifier for widgets that support one (switch and file).
        if ($color !== null) {
            $colorPrefix = $this->_colorPrefix($type, $isSwitch);
            if ($colorPrefix !== null) {
                $colorClass = $this->classMap($colorPrefix . '.' . $color);
                if ($colorClass !== '') {
                    $options = $this->injectClasses($colorClass, $options);
                }
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
        // Inline mode suppresses help text since there's no room for it in
        // a single-row layout.
        $helpHtml = '';
        if ($help !== null && !$isInline) {
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
        if ($isHorizontal) {
            $containerClass = $this->classMap('form.containerHorizontal');
        } elseif ($isInline) {
            $containerClass = $this->classMap('form.containerInline');
        } else {
            $containerClass = $this->classMap('form.container');
        }
        $fieldsetClass = $this->classMap('form.fieldset');
        if ($fieldsetClass === '' || $isInline) {
            $fieldsetClass = $containerClass;
        }

        $options['templateVars']['help'] = $helpHtml;
        $options['templateVars']['containerClass'] = $containerClass;
        $options['templateVars']['fieldsetClass'] = $fieldsetClass;
        $options['templateVars']['errorClass'] = $this->classMap('form.error');

        // Tooltip error feedback: wrap the input in a floating tooltip div
        // containing the error message and suppress the block error message.
        if ($feedbackStyle === 'tooltip' && $isError) {
            $errorText = $this->_formatErrorText($fieldName);
            $tooltipClass = $this->classMap('form.errorTooltip');
            $tooltipOpen = '<div class="' . $tooltipClass . '" data-tip="' . h($errorText) . '">';
            $controlTemplates['formGroup'] = '{{label}}' . $tooltipOpen . '{{input}}</div>';
            $controlTemplates['inputContainerError'] =
                $controlTemplates['inputContainer'] ?? $controlTemplates['inputContainerError'];
        }

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
     * Flattens the validation errors for a given field into a single string
     * suitable for use as a tooltip `data-tip` value.
     */
    protected function _formatErrorText(string $fieldName): string
    {
        $errors = $this->_getContext()->error($fieldName);
        if (!$errors) {
            return '';
        }

        return implode(', ', array_map('strval', $errors));
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
        $submitContainerKey = $this->_align === static::ALIGN_INLINE
            ? 'form.containerInline'
            : 'form.container';
        $options['templateVars']['containerClass'] = $this->classMap($submitContainerKey);

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
     * Renders a read-only control that displays the current value as a
     * paragraph while still submitting it via a hidden field. Uses the same
     * wrapper idiom (fieldset or horizontal div) as a real control so forms
     * stay visually consistent.
     *
     * @param string $fieldName The field name.
     * @param array<string, mixed> $options `label`, `help`, `value`, `escape` honored.
     */
    public function staticControl(string $fieldName, array $options = []): string
    {
        $options += [
            'label' => null,
            'help' => null,
            'value' => null,
            'escape' => true,
        ];

        $value = $options['value'] ?? $this->getSourceValue($fieldName);
        $displayValue = $options['escape'] ? h((string)$value) : (string)$value;

        $staticClass = $this->classMap('form.staticControl');
        $staticParagraph = '<p class="' . $staticClass . '">' . $displayValue . '</p>';
        $hidden = $this->hidden($fieldName, ['value' => (string)$value]);

        $isHorizontal = $this->_align === static::ALIGN_HORIZONTAL;

        // Build label fragment (legend in fieldset mode, label otherwise).
        $labelHtml = '';
        if ($options['label'] !== false) {
            $labelText = is_string($options['label'])
                ? $options['label']
                : $this->_inflect($fieldName);
            $labelClass = $this->_resolveLabelClass('text', $isHorizontal, false, false);
            $tag = (!$isHorizontal && $this->classMap('form.fieldsetLegend') !== '') ? 'legend' : 'label';
            $labelHtml = '<' . $tag . ' class="' . $labelClass . '">' . h($labelText) . '</' . $tag . '>';
        }

        // Build help fragment.
        $helpHtml = '';
        if ($options['help'] !== null) {
            $helperClass = $this->classMap('form.helpText');
            $tpl = $this->formTemplates()['inputHelp']
                ?? $this->_defaultFormTemplates['inputHelp'];
            $helpHtml = strtr($tpl, [
                '{{helperClass}}' => $helperClass,
                '{{text}}' => h($options['help']),
            ]);
        }

        if ($isHorizontal) {
            $containerClass = $this->classMap('form.containerHorizontal');

            return '<div class="' . $containerClass . '">' . $labelHtml . $staticParagraph . $hidden . $helpHtml . '</div>';
        }

        $fieldsetClass = $this->classMap('form.fieldset');
        if ($fieldsetClass === '') {
            $fieldsetClass = $this->classMap('form.container');
        }

        return '<fieldset class="' . $fieldsetClass . '">' . $labelHtml . $staticParagraph . $hidden . $helpHtml . '</fieldset>';
    }

    /**
     * Renders a label tooltip icon (info SVG inside a daisyUI `tooltip`
     * wrapper). The resulting fragment is appended to the label text.
     */
    protected function _renderLabelTooltip(string $tooltipText): string
    {
        $wrapperClass = $this->classMap('form.labelTooltip');
        $iconClass = $this->classMap('form.labelTooltipIcon');

        // Small info SVG (heroicons information-circle). Inlined so this
        // works without requiring the app to ship any icon font.
        $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" '
            . 'viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" '
            . 'class="' . $iconClass . '" aria-hidden="true">'
            . '<path stroke-linecap="round" stroke-linejoin="round" '
            . 'd="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />'
            . '</svg>';

        return '<span class="' . $wrapperClass . '" data-tip="' . h($tooltipText) . '">' . $icon . '</span>';
    }

    /**
     * Returns a human-readable label for a field name, mimicking CakePHP's
     * default label inflection (`user_name` → `User Name`).
     */
    protected function _inflect(string $fieldName): string
    {
        $parts = explode('.', $fieldName);
        $last = end($parts);
        $last = preg_replace('/_id$/', '', (string)$last) ?? $last;

        return ucwords(str_replace('_', ' ', (string)$last));
    }

    /**
     * Override _inputContainerTemplate to apply containerClass from templateVars.
     *
     * @param array<string, mixed> $options
     */
    protected function _inputContainerTemplate(array $options): string
    {
        $type = $options['options']['type'];
        // Remap `select + multiple => checkbox` onto the multicheckbox template
        // so the group fieldset gets its role/aria plumbing.
        if ($type === 'select' && ($options['options']['multiple'] ?? null) === 'checkbox') {
            $type = 'multicheckbox';
        }
        $inputContainerTemplate = $type . 'Container' . $options['errorSuffix'];
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
     * Returns the `form.*` class-map prefix used for size variant lookups,
     * e.g. `form.input`, `form.checkbox`, `form.switch`. Returns null for
     * types that don't support size variants (hidden, submit, etc.).
     */
    protected function _sizePrefix(string $type, bool $isSwitch): ?string
    {
        if ($isSwitch) {
            return 'form.switch';
        }

        return match ($type) {
            'text', 'email', 'password', 'url', 'tel', 'search', 'number' => 'form.input',
            'select' => 'form.select',
            'textarea' => 'form.textarea',
            'checkbox' => 'form.checkbox',
            'radio' => 'form.radio',
            'file' => 'form.file',
            default => null,
        };
    }

    /**
     * Returns the `form.*` class-map prefix used for color variant lookups.
     * Currently only switches and file inputs support colors in the daisyUI
     * class map.
     */
    protected function _colorPrefix(string $type, bool $isSwitch): ?string
    {
        if ($isSwitch) {
            return 'form.switch';
        }

        if ($type === 'file') {
            return 'form.file';
        }

        return null;
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
