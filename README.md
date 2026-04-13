# CakePHP TailwindUi Plugin

[![CI](https://github.com/dereuromark/cakephp-tailwind-ui/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/dereuromark/cakephp-tailwind-ui/actions/workflows/ci.yml?query=branch%3Amaster)
[![Coverage Status](https://img.shields.io/codecov/c/github/dereuromark/cakephp-tailwind-ui/master.svg)](https://codecov.io/gh/dereuromark/cakephp-tailwind-ui)
[![Latest Stable Version](https://poser.pugx.org/dereuromark/cakephp-tailwind-ui/v/stable.svg)](https://packagist.org/packages/dereuromark/cakephp-tailwind-ui)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/dereuromark/cakephp-tailwind-ui/license.svg)](LICENSE)
[![Total Downloads](https://poser.pugx.org/dereuromark/cakephp-tailwind-ui/d/total.svg)](https://packagist.org/packages/dereuromark/cakephp-tailwind-ui)

Tailwind CSS / DaisyUI view helpers for CakePHP 5.2+.\
Drop-in replacement for Bootstrap-styled helpers — outputs Tailwind/DaisyUI markup instead.

This plugin is the Tailwind equivalent of [bootstrap-ui](https://github.com/FriendsOfCake/bootstrap-ui).

## Preview

`$this->Form->control()`, `$this->Paginator->links()`, `$this->Flash->render()`, and `$this->Html->badge()` rendered with the default DaisyUI preset:

| | |
|---|---|
| ![Form controls](docs/screenshots/daisyui-forms.png) | ![Articles index](docs/screenshots/daisyui-articles-index.png) |
| ![Buttons](docs/screenshots/daisyui-buttons.png) | ![Alerts](docs/screenshots/daisyui-alerts.png) |

See [docs/](docs/README.md) for the full set of screenshots and documentation.

## Installation

```bash
composer require dereuromark/cakephp-tailwind-ui
```

Load the plugin:
```bash
bin/cake plugin load TailwindUi
```

## Quick Start

In your `AppView::initialize()`:

```php
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

All `$this->Form->control()`, `$this->Paginator->links()`, `$this->Flash->render()`, and `$this->Breadcrumbs->render()` calls now output DaisyUI-styled markup.

## Class Map

The plugin uses a configurable class map. DaisyUI is the default. To switch to KTUI (Metronic):

```php
// In config/bootstrap.php or Application::bootstrap()
Configure::write('TailwindUi.classMap', 'ktui');
```

Partial overrides:

```php
Configure::write('TailwindUi.classMap', [
    'form.input' => 'my-custom-input-class',
]);
```

## Helpers

| Helper | Description |
|--------|-------------|
| **FormHelper** | Text, select, checkbox, radio, switch, textarea, file, range, input groups, horizontal layout |
| **PaginatorHelper** | `links()` method with join/flex container |
| **FlashHelper** | Alert rendering with icons |
| **BreadcrumbsHelper** | Breadcrumb navigation |
| **HtmlHelper** | `badge()`, `alert()`, and `icon()` methods |

## Class Map Presets

| Preset | Framework | Usage |
|--------|-----------|-------|
| `daisyui` (default) | [DaisyUI](https://daisyui.com) | No config needed |
| `ktui` | [KTUI/Metronic](https://keenthemes.com/metronic) | `Configure::write('TailwindUi.classMap', 'ktui')` |

Custom presets can be added by placing a PHP file in `config/class_maps/` that returns an array.

## Documentation

- [Installation](docs/installation.md)
- [Class Map](docs/class-map.md)
- [Helpers](docs/helpers.md)
- [Presets](docs/presets.md) — DaisyUI, KTUI, custom
- [Bake Theme](docs/bake.md)
- [Screenshots](docs/README.md#screenshots)
