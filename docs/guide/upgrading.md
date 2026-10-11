# Upgrading

Composer is the authority for the supported PHP and CakePHP versions. The current plugin supports CakePHP 5; these docs do not declare CakePHP 6 compatibility.

When changing CakePHP, Tailwind, or a preset, verify generated markup against your application's forms and layouts. The class maps are framework-specific, so replacing a CSS dependency without updating its preset can leave controls unstyled.

`initializeUi()` replaces the usual helper aliases and selects a plugin layout only while the current layout is `default`. Keep an existing application layout with `['layout' => false]`, and ensure that layout loads your stylesheet.

After changing class maps or updating the plugin, rebuild production CSS. Tailwind must scan the plugin's vendor directory as well as application templates. The bundled DaisyUI layout no longer loads external `themes.css`. Compile every theme used by `TailwindUi.theme` into the application stylesheet; light and dark are the DaisyUI defaults.

Before upgrading DaisyUI or KTUI, check their own release notes and interactive-component requirements. Custom templates and class maps remain application code and need review alongside the dependency update.

The `tailwind_ui copy_layouts` command now copies both layouts and the flash element into `templates/plugin/TailwindUi/`, where CakePHP resolves plugin overrides. Existing files are preserved. If you previously customized `templates/layout/default.php`, keep `['layout' => false]` to continue using it or move that customization into the plugin override path deliberately.
