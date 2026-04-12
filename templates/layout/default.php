<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->fetch('title') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @plugin "daisyui";
    </style>
</head>
<body>
<div class="min-h-screen bg-base-200">
    <div class="container mx-auto px-4 py-8">
        <?php if ($this->helpers()->has('Flash')): ?>
            <?= $this->Flash->render() ?>
        <?php endif ?>
        <?= $this->fetch('content') ?>
    </div>
</div>
</body>
</html>
