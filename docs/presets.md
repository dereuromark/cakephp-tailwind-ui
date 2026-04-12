# Presets

A preset is a PHP file in `config/class_maps/` that returns an array of
class-map overrides. The plugin ships with two:

- `daisyui` — default, no config needed
- `ktui` — for Metronic v9 / KTUI users

You can also create your own.

## DaisyUI (default)

[DaisyUI](https://daisyui.com) is a Tailwind CSS component library with
semantic component classes (`btn`, `input`, `alert`, `badge`, `card`,
`breadcrumbs`, `join`, …). It maps cleanly onto the plugin's class map.

**Activation:** nothing to do — it's the default.

**CSS setup:** see [installation.md](installation.md#daisyui-default-preset).

**Caveats:**

- DaisyUI 4 targets Tailwind CSS 3
- DaisyUI 5 targets Tailwind CSS 4 and uses a different API (`@plugin "daisyui";`)
- The default class map is compatible with DaisyUI 4. For DaisyUI 5 the
  class names are mostly unchanged, but the build setup differs

## KTUI (Metronic)

[KTUI](https://keenthemes.com/metronic) is the proprietary Tailwind
component library that ships with Metronic v9. Classes look like `kt-input`,
`kt-btn`, `kt-card`, `kt-badge`, `kt-select`, etc.

**Activation:**

```php
// config/bootstrap.php
\Cake\Core\Configure::write('TailwindUi.classMap', 'ktui');
```

**CSS setup:** KTUI is not available on a public CDN. You need a local
copy of Metronic and to compile Tailwind v4 against your own template
sources so all the utilities you use get emitted.

A minimal `src/css/styles.css` (Tailwind v4 syntax):

```css
@import 'tailwindcss';

/* Point Tailwind at your CakePHP templates */
@source '../../templates/**/*.php';
@source '../../node_modules/@keenthemes/ktui/src/components/**/*.{ts,js,css}';

/* KTUI's theme config + component classes */
@import './config.ktui.css';
@import '../../node_modules/@keenthemes/ktui/styles.css';
```

Compile with:

```bash
npx @tailwindcss/cli -i src/css/styles.css -o webroot/css/app.css --minify
```

Include in your layout:

```html
<link href="/css/app.css" rel="stylesheet" type="text/css" />
<script src="/js/ktui.min.js" defer></script>
```

**Caveats:**

- KTUI's JavaScript is required for interactive components (drawer,
  dropdown, modal, etc.). Pure CSS components work without JS.
- The Metronic license applies — you must own a Metronic license to
  redistribute KTUI CSS/JS in a public project.
- Because Tailwind v4 tree-shakes utilities based on the sources you
  configure, make sure `@source` covers all your templates or the
  classes emitted by this plugin.

## Custom preset

To add a preset for Flowbite, Preline UI, shadcn, or an internal design
system, drop a PHP file into the plugin's `config/class_maps/` directory.
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
Configure::write('TailwindUi.classMap', 'shadcn');
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

You can also combine a preset with per-key overrides — see
[class-map.md](class-map.md#preset--overrides).
