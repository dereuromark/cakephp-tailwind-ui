# Bake Theme

> **Status:** Planned. Not yet shipped.

The plugin will ship a `TailwindUi` bake theme so you can generate
Tailwind/DaisyUI-styled templates via:

```bash
bin/cake bake template Articles --theme TailwindUi
```

Until then, use your own hand-written templates that call the plugin's
helpers — every `$this->Form->control()`, `$this->Paginator->links()`,
etc. will emit the right classes automatically via the class map.
