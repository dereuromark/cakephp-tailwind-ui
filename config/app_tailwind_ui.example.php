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
         * - 'daisyui' (default) — daisyUI 5 fieldset idiom; ships with the plugin
         * - 'ktui' — Metronic / KTUI preset (div wrapper, no fieldset)
         * - array — inline key-value overrides merged over the daisyui base
         * - null — use the daisyui default
         *
         * Inline arrays may use either the legacy flat shape or the extended
         * nested `['classMap' => [...], 'templates' => [...]]` shape — see
         * docs/class-map.md for the full key reference.
         */
        //'classMap' => 'daisyui',
        //'classMap' => 'ktui',
        //'classMap' => [
        //    'form.input' => 'input w-full input-lg',
        //    'btn.primary' => 'btn-primary shadow-lg',
        //],

        /*
         * Extra overrides merged on top of the active preset.
         *
         * Useful when you want the ktui or daisyui preset *plus* a few
         * tweaks without rewriting the entire map inline.
         */
        //'classMapOverrides' => [
        //    'form.input' => 'input w-full input-lg',
        //    'breadcrumbs' => 'text-sm text-muted-foreground mb-4',
        //
        //    // Custom color variant: pair with `colorVariants` below to make
        //    // it suppress the default fallback color.
        //    'btn.brand' => 'btn-brand',
        //    'badge.brand' => 'badge-brand',
        //],

        /*
         * Promote custom variant names to "color" status. Color variants
         * suppress the per-component default color fallback when present in
         * the user's class list. Modifiers and sizes don't.
         *
         * Without this, `['class' => 'brand']` on a button still gets the
         * default `btn-primary` injected on top. With `brand` listed here,
         * `['class' => 'brand']` produces `btn btn-brand` alone.
         */
        //'colorVariants' => ['brand'],
    ],
];

/*
 * Form helper class-map keys (reference)
 * --------------------------------------
 *
 * The full list lives in `vendor/dereuromark/cakephp-tailwind-ui/config/class_maps/daisyui.php`.
 * Highlights of the form-related keys you can override:
 *
 * Containers and structure
 *   form.container               Per-control wrapper class in vertical mode (mb-4)
 *   form.containerHorizontal     Per-control wrapper class in horizontal mode (flex row)
 *   form.containerInline         Per-control wrapper class in inline mode (empty)
 *   form.inlineWrapper           Outer flex wrapper emitted by create([align => inline])
 *   form.fieldset                Fieldset wrapper class in default mode (fieldset)
 *   form.fieldsetLegend          Legend class inside the fieldset (fieldset-legend)
 *   form.label                   Inline label class (label) — used on single checkboxes, KTUI
 *   form.labelHorizontal         Label class in horizontal layout
 *   form.labelInline             Label class in inline layout (sr-only)
 *   form.helpText                Helper text wrapper class (label text-base-content/60)
 *
 * Widgets
 *   form.input                   Text-style input class
 *   form.select                  Select element class
 *   form.textarea                Textarea element class
 *   form.checkbox                Single checkbox class
 *   form.radio                   Radio input class
 *   form.switch                  Switch (checkbox + switch=true) class (toggle)
 *   form.file                    File input class
 *   form.range                   Range slider class
 *   form.checkboxLabelInline     Inline label wrapping a single checkbox + text
 *
 * Validation
 *   form.validator               Class added to inputs in error state (validator)
 *   form.inputError              Alias for the input error class
 *   form.selectError             Alias for the select error class
 *   form.textareaError           Alias for the textarea error class
 *   form.error                   Block error message wrapper class (label text-error)
 *
 * Sizes (xs / sm / md / lg / xl) — opt in via ['size' => 'lg']
 *   form.input.{size}            input-{size}
 *   form.select.{size}           select-{size}
 *   form.textarea.{size}         textarea-{size}
 *   form.checkbox.{size}         checkbox-{size}
 *   form.radio.{size}            radio-{size}
 *   form.switch.{size}           toggle-{size}
 *   form.file.{size}             file-input-{size}
 *   form.rating.{size}           rating-{size}
 *
 * Colors — opt in via ['color' => 'primary'] (switch and file inputs)
 *   form.switch.{color}          toggle-{color}      (primary/secondary/neutral/accent/success/danger/warning/info)
 *   form.file.{color}            file-input-{color}  (same set + 'ghost')
 *
 * Specialty
 *   form.staticControl           Read-only paragraph class for staticControl()
 *   form.labelTooltip            daisyUI tooltip wrapper for label tooltips
 *   form.labelTooltipIcon        Icon size class inside label tooltip span
 *   form.errorTooltip            Tooltip wrapper for feedbackStyle => 'tooltip'
 *   form.floatingLabel           Class for floating-label wrapper (floating-label)
 *   form.rating                  daisyUI rating container (rating)
 *   form.ratingItem              Per-star input class (mask mask-star-2 ...)
 *   form.ratingHidden            Hidden value=0 radio class (rating-hidden)
 *
 * Input groups (prepend/append)
 *   form.inputGroupContainer     Wrapper class when prepend/append is set (join w-full)
 *   form.inputGroupText          Text addon class (join-item ...)
 *
 * See docs/class-map.md for the full reference, including button, badge,
 * alert, breadcrumbs, card, table, pagination and icon keys.
 */
