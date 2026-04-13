<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View;

use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use TailwindUi\View\UiViewTrait;

class UiViewTraitTest extends TestCase
{
    public function tearDown(): void
    {
        parent::tearDown();
        Configure::delete('TailwindUi');
    }

    public function testInitializeUiKeepsExistingLayout(): void
    {
        $view = new class (new ServerRequest([
            'webroot' => '',
            'base' => '',
            'url' => '/',
            'params' => ['controller' => 'Articles', 'action' => 'index', 'plugin' => null],
        ])) extends View {
            use UiViewTrait;
        };
        $view->setLayout('admin');

        $view->initializeUi();

        $this->assertSame('admin', $view->getLayout());
    }

    public function testInitializeUiSetsPluginLayoutFromDefault(): void
    {
        $view = new class (new ServerRequest([
            'webroot' => '',
            'base' => '',
            'url' => '/',
            'params' => ['controller' => 'Articles', 'action' => 'index', 'plugin' => null],
        ])) extends View {
            use UiViewTrait;
        };

        $view->initializeUi();

        $this->assertSame('TailwindUi.default', $view->getLayout());
    }
}
