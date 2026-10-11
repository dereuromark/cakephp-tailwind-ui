# Presets

A preset is a PHP file in `config/class_maps/` that returns an array of
class-map overrides. The plugin ships with two:

- `daisyui`: default, no config needed
- `ktui`: for Metronic v9 / KTUI users

You can also create your own.

## DaisyUI (default)

[DaisyUI](https://daisyui.com) is a Tailwind CSS component library with
semantic component classes (`btn`, `input`, `alert`, `badge`, `card`,
`breadcrumbs`, `join`, …). It maps cleanly onto the plugin's class map.

**Activation:** nothing to do: it's the default.

**CSS setup:** see [Compile your CSS](guide/css.md).

**Caveats:**

- DaisyUI 5 targets Tailwind CSS 4 and uses the `@plugin "daisyui";`
  stylesheet API
- The default preset is designed around DaisyUI 5's fieldset/legend form idiom
- DaisyUI 4 / Tailwind 3 are still mostly class-compatible, but the build
  setup and some component conventions differ

## KTUI (Metronic)

[KTUI](https://ktui.io/docs) is an open-source Tailwind component library distributed under the [MIT license](https://github.com/keenthemes/ktui). Metronic is a separate template product with its own licensing. The plugin's preset emits classes such as `kt-input`, `kt-btn`, `kt-card`, and `kt-badge`.

Configure the preset before loading view helpers:

```php
\Cake\Core\Configure::write('TailwindUi.classMap', 'ktui');
```

Follow [KTUI's installation instructions](https://ktui.io/docs/installation) to install its package, theme variables, stylesheet, and JavaScript. The CakePHP plugin does not copy or initialize those assets. Scan the plugin's vendor directory when compiling Tailwind, as described in [Compile your CSS](guide/css.md).

Use `TailwindUi.ktui` or a custom application layout that includes the compiled stylesheet and KTUI JavaScript. Interactive components need KTUI's JavaScript and the component-specific markup documented upstream; changing the preset alone does not add application interactions.

## Custom preset

Do not edit files inside `vendor/`; dependency updates replace them. Named presets are loaded from the plugin directory. For an application-owned preset, pass the returned array as shown below.

To add a preset for Flowbite, Preline UI, shadcn, or an internal design
system, keep a PHP class-map file in your application's `config/class_maps/` directory.
Example for shadcn-style classes:

```php
// config/class_maps/shadcn.php
return [
    'form.input' => 'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm',
    'form.select' => 'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm',
    'form.textarea' => 'flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm',
    'form.label' => 'text-sm font-medium leading-none',

    'btn' => 'inline-flex items-center justify-center rounded-md text-sm font-medium h-10 px-4 py-2',
    'btn.primary' => 'bg-primary text-primary-foreground hover:bg-primary/90',
    'btn.secondary' => 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
    'btn.danger' => 'bg-destructive text-destructive-foreground hover:bg-destructive/90',
    // ...
];
```

Any keys you don't set fall back to the DaisyUI default values, so you
only need to override what's different.

Activate it:

```php
\Cake\Core\Configure::write('TailwindUi.classMap', require CONFIG . 'class_maps/shadcn.php');
```

## Per-key overrides

Instead of a full preset you can override just a few keys inline:

```php
Configure::write('TailwindUi.classMap', [
    'form.input' => 'input input-bordered input-lg w-full',
    'btn.primary' => 'btn-primary shadow-lg',
]);
```

These are merged on top of the DaisyUI defaults.

You can also combine a preset with per-key overrides: see
[class-map.md](class-map.md#preset-overrides).
