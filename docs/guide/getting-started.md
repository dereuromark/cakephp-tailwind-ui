# Getting started

Complete [Installation](../installation.md) first. This example uses the default DaisyUI preset and a browser stylesheet for a local prototype.

## Enable the helpers

Update `src/View/AppView.php`, preserving any helpers your application already loads:

```php
<?php
declare(strict_types=1);

namespace App\View;

use Cake\View\View;
use TailwindUi\View\UiViewTrait;

class AppView extends View
{
    use UiViewTrait;

    public function initialize(): void
    {
        parent::initialize();
        $this->initializeUi();
    }
}
```

This registers `Form`, `Html`, `Flash`, `Paginator`, and `Breadcrumbs` under their usual aliases. If your layout is still `default`, the trait selects `TailwindUi.default` (`TailwindUi.ktui` for the KTUI preset). A layout you already selected is preserved.

To keep the application's default layout, use `$this->initializeUi(['layout' => false])`. A string selects a specific layout instead, for example `['layout' => 'admin']`.

## Load CSS and render a form

The plugin emits class names and HTML. Its bundled layout does not load a stylesheet automatically. Replace the contents of a template rendered by an existing controller action, such as `templates/Pages/home.php`, with this example. The stock CakePHP home template disables the layout; remove that call so the CSS block is rendered:

```php
<?php
$this->append('css');
?>
<link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<?php
$this->end();
?>

<main class="mx-auto max-w-lg p-6">
    <h1 class="mb-4 text-2xl font-bold">Your first form</h1>
    <?= $this->Form->create(null) ?>
    <?= $this->Form->control('name', ['label' => 'Your name']) ?>
    <?= $this->Form->control('email', ['type' => 'email']) ?>
    <?= $this->Form->button('Preview', ['type' => 'button', 'class' => 'primary']) ?>
    <?= $this->Form->end() ?>
</main>
```

Open that page in your browser. The fields should have DaisyUI styling and the button should use its primary color. This example displays a form; saving submitted values still needs an application controller and model.

The browser build is for development. For deployment, replace the external assets with a [compiled stylesheet](css.md). The example follows the [official DaisyUI CDN setup](https://daisyui.com/docs/cdn/); your application's content security policy must permit those external assets while using it.

## If the page looks unstyled

Check that the stylesheet and browser script loaded successfully. When you keep your own layout, it must output the `css` block, for example `<?= $this->fetch('css') ?>`, inside its `<head>`.

If helpers still emit CakePHP's default markup, confirm `initializeUi()` runs after `parent::initialize()`. Class-map changes must happen before the helpers load. Use [Helpers](../helpers.md) for individual methods and [Class map](../class-map.md) for customization.
