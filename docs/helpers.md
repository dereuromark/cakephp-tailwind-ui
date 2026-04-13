# Helpers

Every helper extends its CakePHP core counterpart and reads classes from
the [class map](class-map.md). Your existing `$this->Form->control()` calls
work unchanged — they just emit Tailwind/DaisyUI markup instead of Bootstrap.

## FormHelper

`TailwindUi\View\Helper\FormHelper` extends `Cake\View\Helper\FormHelper`.

### Alignment

```php
$this->Form->create($article);                        // vertical (default)
$this->Form->create($article, ['align' => 'horizontal']);
```

Horizontal layout uses a flex container from `form.containerHorizontal` and
a fixed-width label from `form.labelHorizontal`.

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

Rendered below the input with `form.helpText` and `aria-describedby`.

### Input groups (prepend/append)

```php
$this->Form->control('price', ['prepend' => '$']);
$this->Form->control('website', ['prepend' => 'https://']);
$this->Form->control('email', ['append' => '@example.com']);
```

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
`TailwindUi.classMapOverrides`) makes `['class' => 'brand']` work without
any helper changes.

### Validation errors

When a field has errors, the input gets the appropriate error class
(`form.inputError` / `form.selectError` / `form.textareaError`) and the
error message is rendered with `form.error`.

## PaginatorHelper

`TailwindUi\View\Helper\PaginatorHelper` adds a `links()` method that wraps
first/prev/numbers/next/last in a container with the `pagination` class:

```php
<?= $this->Paginator->links() ?>
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

`TailwindUi\View\Helper\HtmlHelper` adds two methods on top of CakePHP's
core `HtmlHelper`:

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
outputs inline SVG (Heroicons by default); KTUI preset outputs
`<i class="ki-filled ki-search"></i>`.
