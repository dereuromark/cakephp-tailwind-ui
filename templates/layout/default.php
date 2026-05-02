<?php
/**
 * Default layout for the TailwindUi (DaisyUI) preset.
 *
 * Expects a compiled Tailwind + DaisyUI CSS bundle to be linked via the
 * 'css' view block (e.g. by calling `$this->Html->css('app', ['block' => true])`
 * from your view) or copied/overridden in your app. The KTUI layout uses
 * the same pattern (templates/layout/ktui.php).
 *
 * For an instant prototype with the (NOT production-grade) browser build,
 * see the commented snippet below — but ship a compiled bundle for any
 * real deployment, both for performance and so you can run under a strict
 * Content-Security-Policy without `'unsafe-inline'` for `style-src` /
 * `script-src`.
 *
 * @var \Cake\View\View $this
 */
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($this->fetch('title')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    <?php
    // Prototype-only fallback (uncomment for quick start, NOT for production):
    //   <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    //   <style type="text/tailwindcss">@plugin "daisyui";</style>
    // For production, link a compiled bundle from your app instead, e.g.:
    //   $this->Html->css('app', ['block' => true]);
    ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
<div class="min-h-screen bg-base-200">
    <div class="container mx-auto px-4 py-8">
        <?php if ($this->helpers()->has('Flash')) : ?>
            <?= $this->Flash->render() ?>
        <?php endif ?>
        <?= $this->fetch('content') ?>
    </div>
</div>
</body>
</html>
