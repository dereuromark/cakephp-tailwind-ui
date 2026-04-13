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
| `form.checkbox` | `<input type="checkbox">` class |
| `form.radio` | `<input type="radio">` class |
| `form.switch` | `<input type="checkbox">` class when `'switch' => true` |
| `form.file` | File input class |
| `form.range` | Range input class |
| `form.label` | Default field label class |
| `form.labelHorizontal` | Label class in horizontal layout |
| `form.helpText` | Help text block class |
| `form.error` | Validation error block class |
| `form.inputError` | Class added to input when field has error |
| `form.selectError` | Class added to select when field has error |
| `form.textareaError` | Class added to textarea when field has error |
| `form.container` | Container class for each control in vertical layout |
| `form.containerHorizontal` | Container class for each control in horizontal layout |
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
Any custom key matching `btn.*` added via overrides is automatically
recognized as a modifier (add the name to the `colorVariants` list to
treat it as a color).

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

### Cards, tables

`card`, `card.header`, `card.body`, `card.footer`, `card.title`, `table`.

## Adding a custom preset

Create `config/class_maps/mypreset.php` in the plugin directory (or in your
app if you use a loader). The file must return an array mapping keys to
class strings. Any keys you don't set fall back to the DaisyUI defaults.

```php
// config/class_maps/mypreset.php
return [
    'form.input' => 'my-input',
    'btn' => 'my-btn',
    // ...
];
```

Activate it:

```php
Configure::write('TailwindUi.classMap', 'mypreset');
```
