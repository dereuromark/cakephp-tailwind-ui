# Class Map System

The Class Map is the core abstraction of this plugin. Every helper looks up
its CSS classes from a flat key → class-string map, so the same helper code
can emit DaisyUI, KTUI, or any custom Tailwind component library's markup
just by swapping the map.

## Resolution order

When the first helper method runs, the class map is resolved in this order:

1. Load `config/class_maps/daisyui.php` as the **base**
2. If `Configure::read('TailwindUi.classMap')` is a **string**, load that
   preset from `config/class_maps/{name}.php` and merge over the base
3. If `Configure::read('TailwindUi.classMap')` is an **array**, merge those
   key/values over the current map (partial override)
4. If `Configure::read('TailwindUi.classMapOverrides')` is an array, merge
   those on top (so you can combine a preset with extra overrides)

The resolved map is cached on the helper instance for the request.

## Usage

### Use a preset

```php
// config/bootstrap.php
use Cake\Core\Configure;

Configure::write('TailwindUi.classMap', 'ktui');
```

### Override individual keys

```php
Configure::write('TailwindUi.classMap', [
    'form.input' => 'input input-bordered w-full text-base',
    'btn.primary' => 'btn-primary shadow-lg',
]);
```

### Preset + overrides

```php
Configure::write('TailwindUi.classMap', 'ktui');
Configure::write('TailwindUi.classMapOverrides', [
    'form.input' => 'kt-input kt-input-lg',
]);
```

## Keys

The full list of keys is in `config/class_maps/daisyui.php`. Summary:

### Forms

| Key | Purpose |
|---|---|
| `form.input` | `<input type="text|email|...">` class |
| `form.select` | `<select>` class |
| `form.textarea` | `<textarea>` class |
| `form.input.xs` / `.sm` / `.md` / `.lg` / `.xl` | Input size variants (`['size' => 'lg']`) |
| `form.select.xs` … `.xl` | Select size variants |
| `form.textarea.xs` … `.xl` | Textarea size variants |
| `form.checkbox` | `<input type="checkbox">` class |
| `form.radio` | `<input type="radio">` class |
| `form.switch` | `<input type="checkbox">` class when `'switch' => true` |
| `form.file` | File input class |
| `form.range` | Range input class |
| `form.label` | Default field label class (used on single checkboxes and KTUI forms) |
| `form.fieldset` | Outer fieldset class in fieldset layout mode |
| `form.fieldsetLegend` | Legend class in fieldset layout mode (empty string in KTUI) |
| `form.helperLabel` | daisyUI helper-label class (used for help text paragraphs) |
| `form.validator` | daisyUI 5 `validator` class added to inputs with errors |
| `form.labelHorizontal` | Label class in horizontal layout |
| `form.helpText` | Help text block class |
| `form.error` | Validation error block class |
| `form.inputError` | Class added to input when field has error |
| `form.selectError` | Class added to select when field has error |
| `form.textareaError` | Class added to textarea when field has error |
| `form.container` | Container class for each control in vertical (non-fieldset) layout |
| `form.containerHorizontal` | Container class for each control in horizontal layout |
| `form.checkboxLabelWrapper` | Wrapper class for the single-checkbox label row |
| `form.checkboxLabelInline` | Class on the `<label>` that wraps a single checkbox + its text |
| `form.inputGroupContainer` | Wrapper class when prepend/append is used |
| `form.inputGroupText` | Addon (prepend/append) class |

### Buttons

| Key | Purpose |
|---|---|
| `btn` | Base `<button>`/submit class |
| `btn.primary` `btn.secondary` `btn.neutral` `btn.accent` | Color variants |
| `btn.success` `btn.danger` `btn.warning` `btn.info` | Semantic color variants |
| `btn.outline` `btn.soft` `btn.dash` `btn.ghost` `btn.link` | Style modifiers |
| `btn.xs` `btn.sm` `btn.md` `btn.lg` `btn.xl` | Sizes |

Colors and modifiers stack freely: `['class' => 'soft primary']` emits
`btn btn-soft btn-primary`. Sizes and modifiers don't suppress the
default color — `['class' => 'ghost']` still gets `btn-primary` applied.

Custom keys added via `TailwindUi.classMapOverrides` are recognized
automatically — but as **modifiers**, so the default color still applies
on top (`['class' => 'brand']` → `btn btn-brand btn-primary`). To make a
custom key act as a standalone color, promote it via Configure:

```php
Configure::write('TailwindUi.colorVariants', ['brand']);
```

### Alerts / flash

| Key | Purpose |
|---|---|
| `alert` | Base alert class |
| `alert.success` `alert.error` `alert.warning` `alert.info` `alert.default` | Type variants |

### Badges

| Key | Purpose |
|---|---|
| `badge` | Base badge class |
| `badge.primary` `badge.secondary` `badge.neutral` `badge.accent` | Color variants |
| `badge.success` `badge.danger` `badge.warning` `badge.info` | Semantic color variants |
| `badge.outline` `badge.soft` `badge.dash` `badge.ghost` | Style modifiers |
| `badge.xs` `badge.sm` `badge.md` `badge.lg` `badge.xl` | Sizes |

Same stacking rules as buttons: colors + modifiers compose, the default
(`secondary`) only kicks in when no color keyword is present.

### Pagination

| Key | Purpose |
|---|---|
| `pagination` | Container class wrapping the page links |
| `pagination.item` | Each `<a>` class |
| `pagination.active` | Additional class on the current page link |
| `pagination.disabled` | Additional class on disabled (first/prev on page 1) |

### Breadcrumbs

| Key | Purpose |
|---|---|
| `breadcrumbs` | Container class |
| `breadcrumbs.item` | Each `<li>` class |
| `breadcrumbs.active` | Additional class on the last crumb |

### Icons

| Key | Purpose |
|---|---|
| `icon.tag` | HTML tag (`svg`, `i`, …) |
| `icon.namespace` | Icon set name (for the classes generated) |
| `icon.prefix` | Class prefix (e.g. `ki-filled` for KTUI) |
| `icon.size` | Size class (e.g. `size-5`) |

In default SVG mode, the helper ships a small built-in path map for the
icons used by the plugin/docs. For app-specific icons, pass a `content`
option to `HtmlHelper::icon()` with the SVG path markup you want rendered.

### Cards, tables

`card`, `card.header`, `card.body`, `card.footer`, `card.title`, `table`.

## Preset file format

Presets may return either the legacy flat class-map array:

```php
return [
    'form.input' => 'my-input',
    'btn' => 'my-btn',
];
```

…or the extended nested shape, which also allows overriding FormHelper's
container templates:

```php
return [
    'classMap' => [
        'form.input' => 'my-input',
        'btn' => 'my-btn',
    ],
    'templates' => [
        'inputContainer' => '<fieldset class="{{fieldsetClass}}">{{content}}{{help}}</fieldset>',
        'inputContainerError' => '<fieldset class="{{fieldsetClass}}">{{content}}{{error}}{{help}}</fieldset>',
        'label' => '<legend{{attrs}}>{{text}}</legend>',
        'inputHelp' => '<p class="{{helperClass}}">{{text}}</p>',
    ],
];
```

Both shapes are auto-detected. Use the nested shape when your framework's
form idiom requires different wrapper markup (e.g. daisyUI 5 uses
`<fieldset>` and `<legend>`, KTUI uses plain `<div>` and `<label>`).

Template overrides are **ignored in horizontal alignment mode** —
horizontal layout always uses the plugin's built-in div-based templates
because `<legend>` doesn't compose with a two-column flex layout.

### Available form template keys

| Key | Used for |
|---|---|
| `inputContainer` / `inputContainerError` | wrapper around text/select/textarea/file/date |
| `radioContainer` / `radioContainerError` | wrapper around radio groups |
| `multicheckboxContainer` / `multicheckboxContainerError` | wrapper around multicheckbox groups |
| `label` | the label element rendered before the input (becomes `<legend>` in fieldset mode) |
| `inputHelp` | the help-text fragment rendered inside the container |

Available placeholders inside container templates: `{{content}}`,
`{{help}}`, `{{error}}`, `{{containerClass}}`, `{{fieldsetClass}}`,
`{{groupId}}`, `{{errorClass}}`.

## Adding a custom preset

Create `config/class_maps/mypreset.php` in the plugin directory (or in your
app if you use a loader). Any keys you don't set fall back to the DaisyUI
defaults. Activate it:

```php
Configure::write('TailwindUi.classMap', 'mypreset');
```
