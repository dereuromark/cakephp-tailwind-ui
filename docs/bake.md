# Bake Theme

The plugin ships a `TailwindUi` bake theme that generates Tailwind/DaisyUI-styled
CakePHP templates via the standard bake tool.

## Installation

The bake theme requires `cakephp/bake` (a dev/build tool, not a runtime dependency):

```bash
composer require --dev cakephp/bake
```

Make sure the plugin is loaded in your `config/plugins.php`:

```php
'Bake' => ['onlyCli' => true, 'optional' => true],
'TailwindUi' => [],
```

## Usage

Generate all four templates (index, view, add, edit) for a model:

```bash
bin/cake bake template Articles --theme TailwindUi
```

Generate a single template type:

```bash
bin/cake bake template Articles index --theme TailwindUi
bin/cake bake template Articles view --theme TailwindUi
bin/cake bake template Articles add --theme TailwindUi
bin/cake bake template Articles edit --theme TailwindUi
```

Force overwrite existing templates:

```bash
bin/cake bake template Articles --theme TailwindUi --force
```

Bake templates for all models at once:

```bash
bin/cake bake all --theme TailwindUi
```

## What is generated

For each model, bake produces four template files in `templates/{ModelName}/`:

| File | Description |
|------|-------------|
| `index.php` | Paginated list in a `card bg-base-100 shadow` with a `table table-zebra`, sortable columns via `Paginator->sort()`, action buttons (view/edit/delete), and `Paginator->links()` pagination |
| `view.php` | Detail card with a `<table>` of label/value pairs; related records from `HasMany`/`BelongsToMany` associations shown in separate cards below |
| `add.php` | Form card calling `Form->control()` for each field; FK fields rendered as selects using the associated model list; Cancel + Submit buttons |
| `edit.php` | Same as add, with an additional Delete `postLink` in the action bar |

All templates include:
- Breadcrumbs via `$this->Breadcrumbs->add()` / `$this->Breadcrumbs->render()`
- Page title via `$this->assign('title', ...)`
- `$this->fetch('postLink')` for delete confirmation forms
- BelongsTo associations rendered as links in both index and view

## Customization

To customize the generated templates for your app, copy the bake theme files into your
app's `templates/bake/` directory and modify them there. Your app's templates take
precedence over the plugin's:

```bash
mkdir -p templates/bake/Template templates/bake/element
cp vendor/dereuromark/cakephp-tailwind-ui/templates/bake/Template/*.twig templates/bake/Template/
cp vendor/dereuromark/cakephp-tailwind-ui/templates/bake/element/form.twig templates/bake/element/
```

Then edit the copied `.twig` files to suit your layout, add extra fields, change
class names, or adjust the structure. The bake variable reference is the same as
any CakePHP bake theme — see the
[bake documentation](https://book.cakephp.org/bake/3/en/development.html) for
the full list of available variables (`$fields`, `$associations`, `$primaryKey`, etc.).
