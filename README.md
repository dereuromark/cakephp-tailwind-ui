# CakePHP TailwindUi Plugin

[![CI](https://github.com/dereuromark/cakephp-tailwind-ui/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/dereuromark/cakephp-tailwind-ui/actions/workflows/ci.yml?query=branch%3Amaster)
[![Coverage Status](https://img.shields.io/codecov/c/github/dereuromark/cakephp-tailwind-ui/master.svg)](https://codecov.io/gh/dereuromark/cakephp-tailwind-ui)
[![Latest Stable Version](https://poser.pugx.org/dereuromark/cakephp-tailwind-ui/v/stable.svg)](https://packagist.org/packages/dereuromark/cakephp-tailwind-ui)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/dereuromark/cakephp-tailwind-ui/license.svg)](LICENSE)
[![Total Downloads](https://poser.pugx.org/dereuromark/cakephp-tailwind-ui/d/total.svg)](https://packagist.org/packages/dereuromark/cakephp-tailwind-ui)
[![Coding Standards](https://img.shields.io/badge/cs-PhpCollective-purple.svg?style=flat-square)](https://github.com/php-collective/code-sniffer)

Tailwind CSS and DaisyUI view helpers for CakePHP forms, pagination, flash messages, and navigation.

Read the [documentation website](https://dereuromark.github.io/cakephp-tailwind-ui/), starting with [Installation](https://dereuromark.github.io/cakephp-tailwind-ui/installation) and [Getting started](https://dereuromark.github.io/cakephp-tailwind-ui/guide/getting-started).

## Preview

`$this->Form->control()`, `$this->Paginator->links()`, `$this->Flash->render()`, and `$this->Html->badge()` rendered with the default DaisyUI preset:

| | |
|---|---|
| ![Form controls](docs/screenshots/daisyui-forms.png) | ![Articles index](docs/screenshots/daisyui-articles-index.png) |
| ![Buttons](docs/screenshots/daisyui-buttons.png) | ![Alerts](docs/screenshots/daisyui-alerts.png) |

See the [screenshot gallery](https://dereuromark.github.io/cakephp-tailwind-ui/screenshots) for both presets.

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
| `ktui` | [KTUI](https://ktui.io/docs) | `Configure::write('TailwindUi.classMap', 'ktui')` |

Keep custom presets in your application and pass their returned array to `TailwindUi.classMap`; see the [preset guide](https://dereuromark.github.io/cakephp-tailwind-ui/presets#custom-preset).

## Documentation

- [Installation](https://dereuromark.github.io/cakephp-tailwind-ui/installation)
- [Getting started](https://dereuromark.github.io/cakephp-tailwind-ui/guide/getting-started)
- [Compile your CSS](https://dereuromark.github.io/cakephp-tailwind-ui/guide/css)
- [Helpers](https://dereuromark.github.io/cakephp-tailwind-ui/helpers)
- [Class maps and presets](https://dereuromark.github.io/cakephp-tailwind-ui/presets)
- [Screenshots](https://dereuromark.github.io/cakephp-tailwind-ui/screenshots)
