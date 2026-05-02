/**
 * CSP-friendly dismiss handler for TailwindUi alerts/flashes.
 *
 * Activate by including the asset in your layout:
 *   <?= $this->Html->script('TailwindUi.dismiss') ?>
 *
 * Markup contract: a click on any element carrying
 *   data-tailwind-ui-dismiss="<selector>"
 * removes the closest ancestor matching <selector>. Defaults to
 * '[role=alert]' when the attribute value is empty.
 */
(function () {
    'use strict';

    document.addEventListener('click', function (event) {
        var target = event.target;
        if (!target || typeof target.closest !== 'function') {
            return;
        }
        var trigger = target.closest('[data-tailwind-ui-dismiss]');
        if (!trigger) {
            return;
        }
        var selector = trigger.getAttribute('data-tailwind-ui-dismiss') || '[role=alert]';
        var dismissable = trigger.closest(selector);
        if (dismissable && dismissable.parentNode) {
            dismissable.parentNode.removeChild(dismissable);
        }
    });
})();
