# Installation

Run this command from your CakePHP application's root directory:

```bash
composer require dereuromark/cakephp-tailwind-ui
```

Composer checks the supported PHP and CakePHP versions. Load the plugin once:

```bash
bin/cake plugin load TailwindUi
```

If it is already registered in `src/Application.php` or `config/plugins.php`, keep that registration. The plugin provides helpers and commands; it adds no routes, database tables, or middleware.

Continue with [Getting started](guide/getting-started.md) to enable the helpers and display your first form. For a production stylesheet, follow [Compile your CSS](guide/css.md).
