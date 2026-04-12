<?php
declare(strict_types=1);

namespace TailwindUi\View\Helper;

use Cake\View\Helper;
use Cake\View\View;
use function Cake\Core\h;

class FlashHelper extends Helper
{
    use ClassMapTrait;

    /**
     * @var array<string, mixed>
     */
    public array $helpers = ['Html' => ['className' => 'TailwindUi.Html']];

    /**
     * Default configuration.
     *
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'iconMap' => [
            'default' => 'information-circle',
            'success' => 'check-circle',
            'error' => 'exclamation-triangle',
            'info' => 'information-circle',
            'warning' => 'exclamation-triangle',
        ],
        'defaultElement' => 'TailwindUi.flash/default',
    ];

    /**
     * Renders flash messages from the session.
     *
     * @param string $key Session key to read flash messages from.
     * @param array<string, mixed> $options Options.
     * @return string|null Rendered HTML or null if no messages.
     */
    public function render(string $key = 'flash', array $options = []): ?string
    {
        $session = $this->_View->getRequest()->getSession();
        $messages = $session->read('Flash.' . $key);

        if (empty($messages)) {
            return null;
        }

        $out = '';
        $iconMap = $this->getConfig('iconMap');
        $defaultElement = $this->getConfig('defaultElement');

        foreach ($messages as $message) {
            $type = $message['params']['type'] ?? 'default';
            $alertClass = trim($this->classMap('alert') . ' ' . $this->classMap('alert.' . $type));
            $icon = $iconMap[$type] ?? $iconMap['default'];

            $params = ($message['params'] ?? []) + [
                'alertClass' => $alertClass,
                'icon' => $icon,
            ];

            $element = $message['element'] ?? $defaultElement;

            $out .= $this->_View->element($element, [
                'message' => $message['message'],
                'params' => $params,
            ]);
        }

        $session->delete('Flash.' . $key);

        return $out;
    }
}
