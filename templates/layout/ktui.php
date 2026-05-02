<?php
/**
 * Default layout for the KTUI (Metronic) preset.
 *
 * Activate with:
 *   Configure::write('TailwindUi.classMap', 'ktui');
 *
 * Expects a compiled Tailwind CSS bundle at /css/app.css (or similar)
 * that includes Metronic's KTUI component library. Override by copying
 * this file into your app's templates/layout/ directory.
 *
 * @var \Cake\View\View $this
 */
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($this->fetch('title')) ?></title>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('script') ?>
</head>
<body class="antialiased flex h-full text-base text-foreground bg-muted/40">
<div class="flex min-h-screen w-full">
    <main class="flex-1 p-6">
        <?php if ($this->helpers()->has('Flash')) : ?>
            <?= $this->Flash->render() ?>
        <?php endif ?>
        <?= $this->fetch('content') ?>
    </main>
</div>
</body>
</html>
