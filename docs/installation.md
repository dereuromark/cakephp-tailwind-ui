# Installation

## Requirements

- PHP 8.2 or higher
- CakePHP 5.2 or higher

## Via Composer

```bash
composer require dereuromark/cakephp-tailwind-ui
```

Load the plugin:

```bash
bin/cake plugin load TailwindUi
```

Or manually in `src/Application.php`:

```php
public function bootstrap(): void
{
    parent::bootstrap();
    $this->addPlugin('TailwindUi');
}
```

## Enable the helpers

In `src/View/AppView.php`:

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

`initializeUi()` accepts an options array:

- `layout` (default `true`) — set the plugin's default layout
  (`TailwindUi.default`). Pass a string to use a custom layout, or `false`
  to keep whatever layout is already set.

## CSS framework

The plugin emits class names only — you must load Tailwind CSS and
DaisyUI (or whichever preset you're using) yourself.

### DaisyUI (default preset)

The default preset targets modern DaisyUI 5 / Tailwind 4 setups.

During development you can use the CDN:

```html
<link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
```

For production, install via npm:

```bash
npm install -D tailwindcss@4 daisyui@5
```

Then import DaisyUI from your Tailwind entry stylesheet:

```css
@import 'tailwindcss';
@plugin "daisyui";
```

If you're still on DaisyUI 4 / Tailwind 3, the emitted component class
names are mostly compatible, but the recommended build setup differs.

### KTUI (Metronic preset)

See [presets.md](presets.md#ktui) for details. KTUI requires compiling
Tailwind v4 against your own template sources.

## Next steps

- [Class Map](class-map.md) — understand how classes are resolved
- [Helpers](helpers.md) — what each helper provides
- [Presets](presets.md) — switch between DaisyUI, KTUI, or your own
