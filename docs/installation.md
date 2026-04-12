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

During development you can use the CDN:

```html
<link href="https://cdn.jsdelivr.net/npm/daisyui@4/dist/full.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
```

For production, install via npm:

```bash
npm install -D tailwindcss@3 daisyui@4
```

Then set up `tailwind.config.js` to scan your templates:

```js
module.exports = {
  content: ['./templates/**/*.php'],
  plugins: [require('daisyui')],
};
```

### KTUI (Metronic preset)

See [presets.md](presets.md#ktui) for details. KTUI requires compiling
Tailwind v4 against your own template sources.

## Next steps

- [Class Map](class-map.md) — understand how classes are resolved
- [Helpers](helpers.md) — what each helper provides
- [Presets](presets.md) — switch between DaisyUI, KTUI, or your own
