# Helpers

Every helper extends its CakePHP core counterpart and reads classes from
the [class map](class-map.md). Your existing `$this->Form->control()` calls
work unchanged — they just emit Tailwind/DaisyUI markup instead of Bootstrap.

## FormHelper

`TailwindUi\View\Helper\FormHelper` extends `Cake\View\Helper\FormHelper`.

### Control markup (fieldset, daisyUI 5 idiom)

In the default alignment, every labeled control is wrapped in a
`<fieldset class="fieldset">` with its label rendered as
`<legend class="fieldset-legend">`, matching daisyUI 5's recommended
form idiom:

```html
<fieldset class="fieldset">
  <legend class="fieldset-legend">Title</legend>
  <input class="input w-full" type="text" name="title">
  <p class="label text-base-content/60">Helper text</p>
</fieldset>
```

Single checkboxes keep their inline-flex label wrapper (no fieldset).
Submit buttons and hidden fields are never wrapped.

Presets control the container markup via a `templates` block in the
preset file — see [the class map guide](class-map.md) for details.
The KTUI preset ships a `<div class="mb-4">` wrapper instead, because
KTUI has no `fieldset-legend` equivalent.

### Alignment

```php
$this->Form->create($article);                              // vertical, fieldset wrapper
$this->Form->create($article, ['align' => 'horizontal']);  // div wrapper, flex row
$this->Form->create($article, ['align' => 'inline']);     // search/filter bar layout
```

Horizontal layout keeps the `<div>` wrapper from `form.containerHorizontal`
and a fixed-width label from `form.labelHorizontal`. Fieldsets are
disabled in horizontal mode because a `<legend>` doesn't compose with
the two-column flex layout.

Inline layout wraps all controls in a single
`<div class="flex flex-wrap items-end gap-3 mb-4">` (from
`form.inlineWrapper`), hides each label as `sr-only` (still readable
by screen readers), and suppresses help text. Intended for search
bars and filter rows. Widths are user-controlled — pass
`['class' => 'w-48']` or similar on each control to size them.

### Every CakePHP input type

```php
$this->Form->control('title');
$this->Form->control('email', ['type' => 'email']);
$this->Form->control('body', ['type' => 'textarea']);
$this->Form->control('status', ['type' => 'select', 'options' => [...]]);
$this->Form->control('active', ['type' => 'checkbox']);
$this->Form->control('tags._ids', ['type' => 'select', 'multiple' => 'checkbox', 'options' => $tags]);
$this->Form->control('role', ['type' => 'radio', 'options' => [...]]);
$this->Form->control('file', ['type' => 'file']);
$this->Form->control('published_date', ['type' => 'date']);
```

### Switch checkbox

```php
$this->Form->control('active', ['type' => 'checkbox', 'switch' => true]);
```

Renders with the `form.switch` class (DaisyUI: `toggle`, KTUI: `kt-switch`).

### Help text

```php
$this->Form->control('username', [
    'help' => 'Choose a unique username, 3–20 characters.',
]);
```

Rendered inside the fieldset as `<p class="label text-base-content/60">`
(daisyUI 5 helper-label styling) with an `id` and matching
`aria-describedby` on the input. KTUI uses `<div class="text-muted-foreground text-2sm">`.

### Size variants

```php
$this->Form->control('title', ['size' => 'lg']);
$this->Form->control('status', ['options' => [...], 'size' => 'sm']);
$this->Form->control('body', ['size' => 'xl']);
$this->Form->control('published', ['size' => 'lg']);                       // checkbox
$this->Form->control('role', ['type' => 'radio', 'options' => [...], 'size' => 'sm']);
$this->Form->control('active', ['switch' => true, 'size' => 'lg']);
$this->Form->control('avatar', ['type' => 'file', 'size' => 'lg']);
```

Injects the daisyUI size modifier (`input-lg`, `select-sm`, `textarea-xl`,
`checkbox-lg`, `radio-sm`, `toggle-lg`, `file-input-lg`) via the
`form.{type}.{size}` class map keys. Available sizes: `xs`, `sm`, `md`,
`lg`, `xl`. Unmapped combinations (e.g. KTUI has no size equivalents
for inputs) are silently ignored.

### Color variants

```php
$this->Form->control('active', ['switch' => true, 'color' => 'primary']);
$this->Form->control('avatar', ['type' => 'file', 'color' => 'primary']);
$this->Form->control('logo', ['type' => 'file', 'color' => 'ghost']);
```

Currently supported on switches and file inputs. Resolves to
`form.switch.{color}` / `form.file.{color}` class map keys. Available
names: `primary`, `secondary`, `neutral`, `accent`, `success`, `danger`
(maps to `error`), `warning`, `info`. File inputs additionally support
`ghost`.

### Floating labels

```php
$this->Form->control('email', ['floating' => true]);
$this->Form->control('status', ['options' => $opts, 'floating' => true]);
```

Wraps the input in a daisyUI `<label class="floating-label">` with an
inline `<span>` for the label text. A placeholder is automatically set
(daisyUI's floating-label needs `:placeholder-shown` to detect emptiness),
or you can supply your own. Supported on text-style inputs, selects, and
textareas; ignored for checkbox/radio. Class map key: `form.floatingLabel`.

`'floating' => true` is also a no-op in inline alignment mode (floating
labels need vertical headroom and `sr-only` labels can't host visible
floating text) and on the KTUI preset (KTUI has no floating-label
component, so the option silently degrades to normal rendering).

### Rating

```php
$this->Form->rating('quality');                                // 5 stars, default
$this->Form->rating('quality', ['max' => 10, 'value' => 7]);
$this->Form->rating('quality', ['size' => 'lg', 'allowEmpty' => false]);
```

Renders a daisyUI `rating` component as a horizontal row of radio
inputs styled as star masks. Wrapped in the standard fieldset/horizontal
container so it composes with the rest of the form. Options:

- `max` — number of stars (default `5`).
- `value` — currently selected rating (`null`/`0` for no rating).
- `size` — `xs`/`sm`/`md`/`lg`/`xl`.
- `allowEmpty` — whether to render a hidden "no rating" radio at value `0` so users can clear the rating (default `true`).
- `label`, `help` — same semantics as `control()`.

Class map keys: `form.rating`, `form.ratingItem`, `form.ratingHidden`,
`form.rating.{size}`. KTUI renders a fallback flex row of plain
checkboxes — KTUI has no rating component natively.

Out-of-range values are clamped: `value` greater than `max` highlights
the highest star, negative `value` snaps to the empty radio. Field
validation errors don't currently propagate onto rating inputs (the
underlying widget bypasses CakePHP's standard error class injection);
if you need a visible error state for a rating, render an error
paragraph manually below it.

### Validation errors

When a field has errors, the input gets the `form.validator` class
(`validator` in daisyUI 5, which triggers the built-in error ring).
The error message renders as `<p class="label text-error">` inside
the same fieldset.

### Tooltip error feedback

```php
$this->Form->control('email', ['feedbackStyle' => 'tooltip']);
```

When the field has errors, the input is wrapped in a daisyUI
`tooltip tooltip-error tooltip-open` div containing the error text,
and the block error paragraph below the input is suppressed.

### Label tooltips

```php
$this->Form->control('username', ['tooltip' => 'Must be unique']);
```

Appends a small info icon to the label text, wrapped in a daisyUI
`tooltip` span with the tooltip text as `data-tip`. Uses `form.labelTooltip`
and `form.labelTooltipIcon` class map keys.

### Static control (read-only)

```php
$this->Form->staticControl('slug', ['value' => 'my-post-slug']);
```

Renders the value as a `<p class="py-2 text-base-content">` (from
`form.staticControl`) and adds a hidden field so the value still
submits with the form. Wrapped in the same fieldset/div container
as a regular control, so forms stay visually consistent.

### Input groups (prepend/append)

```php
$this->Form->control('price', ['prepend' => '$']);
$this->Form->control('website', ['prepend' => 'https://']);
$this->Form->control('email', ['append' => '@example.com']);
```

> Note: `prepend`/`append` is **not supported on `type => 'file'` controls**.
> CakePHP's core file widget doesn't compose with the daisyUI `join`
> wrapper, so the keys are silently dropped if passed.

### Submit buttons with variants

```php
$this->Form->submit('Save');                          // primary (default)
$this->Form->submit('Delete', ['class' => 'danger']);
$this->Form->submit('Cancel', ['class' => 'secondary outline sm']);
```

Color variants (`primary`, `secondary`, `neutral`, `accent`, `success`,
`danger`, `warning`, `info`), style modifiers (`outline`, `soft`, `dash`,
`ghost`, `link`), and sizes (`xs`, `sm`, `md`, `lg`, `xl`) are stripped
from the class list and replaced with the equivalent class map values
(e.g. `danger` → `btn-error`). Colors and modifiers combine freely —
`['class' => 'soft primary']` emits `btn btn-soft btn-primary`.

The set of recognized keywords is derived from the class map at runtime,
so adding a custom key (e.g. `btn.brand => 'btn-brand'` via
`TailwindUi.classMapOverrides`) makes `brand` recognized as a **modifier**
without any helper changes — meaning `['class' => 'brand']` emits
`btn btn-brand btn-primary`, because the default primary color still
applies to modifiers.

If you want `brand` to act as a standalone color (suppressing the
primary default), promote it via `Configure`:

```php
Configure::write('TailwindUi.colorVariants', ['brand']);
```

Then `['class' => 'brand']` emits `btn btn-brand`, and `['class' => 'soft brand']`
stacks to `btn btn-soft btn-brand`.

### Validation errors

When a field has errors, the input gets the appropriate error class
(`form.inputError` / `form.selectError` / `form.textareaError`) and the
error message is rendered with `form.error`.

## PaginatorHelper

`TailwindUi\View\Helper\PaginatorHelper` adds a `links()` method that wraps
first/prev/numbers/next/last in a container with the `pagination` class:

```php
<?= $this->Paginator->links() ?>
<?= $this->Paginator->links(['first' => false, 'last' => false]) ?>
```

DaisyUI output:

```html
<div class="join">
    <a class="join-item btn btn-sm" href="...">«</a>
    <a class="join-item btn btn-sm" href="...">‹</a>
    <a class="join-item btn btn-sm btn-active" aria-current="page">2</a>
    <a class="join-item btn btn-sm" href="...">3</a>
    <a class="join-item btn btn-sm" href="...">›</a>
    <a class="join-item btn btn-sm" href="...">»</a>
</div>
```

KTUI output uses `flex items-center gap-1` for the container and
`kt-btn kt-btn-sm kt-btn-outline` for the items.

You can still use the individual `first()`, `prev()`, `numbers()`,
`next()`, `last()` methods from CakePHP core.

## FlashHelper

`TailwindUi\View\Helper\FlashHelper`:

```php
<?= $this->Flash->render() ?>
```

In your controller:

```php
$this->Flash->success('Record saved.');
$this->Flash->error('Could not delete record.');
$this->Flash->warning('This is irreversible.');
$this->Flash->info('Updates available.');
```

Each message is rendered with the alert base class plus the type variant
(e.g. `alert alert-success`), an inline SVG icon, and a dismiss button
that removes the element on click.

## BreadcrumbsHelper

`TailwindUi\View\Helper\BreadcrumbsHelper`:

```php
$this->Breadcrumbs->add('Home', '/');
$this->Breadcrumbs->add('Articles', '/articles');
$this->Breadcrumbs->add('Edit');          // no URL → active crumb
echo $this->Breadcrumbs->render();
```

Output:

```html
<div class="breadcrumbs text-sm">
    <ul>
        <li><a href="/">Home</a></li>
        <li><a href="/articles">Articles</a></li>
        <li><span class="font-semibold">Edit</span></li>
    </ul>
</div>
```

The last crumb automatically gets the `breadcrumbs.active` class.

## HtmlHelper

`TailwindUi\View\Helper\HtmlHelper` adds three methods on top of CakePHP's
core `HtmlHelper`:

### `alert()`

```php
<?= $this->Html->alert('Heads up') ?>                          // info (default)
<?= $this->Html->alert('Saved', ['class' => 'success']) ?>
<?= $this->Html->alert('Failed', ['class' => 'danger']) ?>     // → alert-error
<?= $this->Html->alert('Heads up', ['class' => 'warning']) ?>
<?= $this->Html->alert('<strong>raw</strong>', ['escape' => false]) ?>
<?= $this->Html->alert('Note', ['tag' => 'aside']) ?>
```

Renders a daisyUI `alert` for one-shot inline notices that don't go
through the session/flash pipeline. Variants (`success`, `danger`/
`error`, `warning`, `info`) resolve through the same class-map
machinery as `badge()` and form buttons; `danger` is accepted as an
alias for daisyUI's `error`. Default variant is `info`. Always emits
`role="alert"` for screen readers unless overridden.

### `badge()`

```php
<?= $this->Html->badge('New') ?>                                  // secondary
<?= $this->Html->badge('Active', ['class' => 'success']) ?>
<?= $this->Html->badge('Draft', ['class' => 'warning outline']) ?>
<?= $this->Html->badge('Soft', ['class' => 'soft primary']) ?>
<?= $this->Html->badge('Ghost', ['class' => 'ghost']) ?>
<?= $this->Html->badge('3', ['class' => 'primary sm']) ?>
```

Color variants (`primary`, `secondary`, `neutral`, `accent`, `success`,
`danger`, `warning`, `info`), style modifiers (`outline`, `soft`, `dash`,
`ghost`), and sizes (`xs`, `sm`, `md`, `lg`, `xl`) are resolved from the
class map. Colors and modifiers stack — `['class' => 'soft primary']`
produces a soft-primary badge.

### `icon()`

```php
<?= $this->Html->icon('search') ?>
<?= $this->Html->icon('pencil', ['size' => 'size-4']) ?>
```

Renders an icon tag using the `icon.*` class map keys. DaisyUI preset
outputs inline SVG for the bundled default icon set (`check`,
`check-circle`, `exclamation-triangle`, `information-circle`, `pencil`,
`search`); KTUI preset outputs `<i class="ki-filled ki-search"></i>`.

For app-specific SVGs, pass path content explicitly:

```php
<?= $this->Html->icon('brand', ['content' => '<path d="..." />']) ?>
```
