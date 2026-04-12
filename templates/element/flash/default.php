<?php
/**
 * @var \Cake\View\View $this
 * @var string $message
 * @var array $params
 */
use function Cake\Core\h;

$escape = $params['escape'] ?? true;
$alertClass = $params['alertClass'] ?? 'alert';
$icon = $params['icon'] ?? 'information-circle';
?>
<div class="<?= h($alertClass) ?>" role="alert">
    <?php if ($icon === 'check-circle'): ?>
        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    <?php elseif ($icon === 'exclamation-triangle'): ?>
        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
    <?php else: ?>
        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    <?php endif; ?>
    <span><?= $escape ? h($message) : $message ?></span>
    <button type="button" class="ms-auto" onclick="this.closest('[role=alert]').remove()" aria-label="Close">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>
