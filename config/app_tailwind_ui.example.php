<?php
/**
 * TailwindUi plugin configuration example.
 *
 * Copy this file to your app's `config/` directory as `app_tailwind_ui.php`
 * and load it in your `config/bootstrap.php`:
 *
 *     Configure::load('app_tailwind_ui', 'default');
 *
 * Every key is optional. The defaults are shown commented out.
 */
declare(strict_types=1);

return [
    'TailwindUi' => [
        /*
         * Preset or inline class map.
         *
         * - 'daisyui' (default) — ships with the plugin
         * - 'ktui' — Metronic / KTUI preset
         * - array — inline key-value overrides merged over the daisyui base
         * - null — use the daisyui default
         *
         * See docs/class-map.md for the full list of keys.
         */
        //'classMap' => 'daisyui',
        //'classMap' => 'ktui',
        //'classMap' => [
        //    'form.input' => 'input input-bordered input-lg w-full',
        //    'btn.primary' => 'btn-primary shadow-lg',
        //],

        /*
         * Extra overrides merged on top of the active preset.
         *
         * Useful when you want the ktui or daisyui preset *plus* a few
         * tweaks without rewriting the entire map inline.
         */
        //'classMapOverrides' => [
        //    'form.input' => 'kt-input kt-input-lg',
        //    'breadcrumbs' => 'text-sm text-muted-foreground mb-4',
        //],
    ],
];
