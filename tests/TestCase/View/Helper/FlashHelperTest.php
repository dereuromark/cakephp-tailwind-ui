<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper;

use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use TailwindUi\View\Helper\FlashHelper;

class FlashHelperTest extends TestCase
{
    protected View $View;

    protected FlashHelper $Flash;

    protected Session $Session;

    public function setUp(): void
    {
        parent::setUp();
        Configure::delete('TailwindUi');

        $this->Session = new Session();

        $request = new ServerRequest([
            'webroot' => '',
            'base' => '',
            'url' => '/articles/index',
            'params' => ['controller' => 'Articles', 'action' => 'index', 'plugin' => null],
            'session' => $this->Session,
        ]);
        $this->View = new View($request);
        $this->View->loadHelper('Html', ['className' => 'TailwindUi.Html']);
        $this->Flash = new FlashHelper($this->View);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Configure::delete('TailwindUi');
        unset($this->Flash, $this->View, $this->Session);
    }

    public function testRenderReturnsNullWhenNoMessages(): void
    {
        $result = $this->Flash->render();
        $this->assertNull($result);
    }

    public function testRenderSuccess(): void
    {
        $this->Session->write('Flash.flash', [
            [
                'message' => 'Item saved successfully.',
                'key' => 'flash',
                'element' => 'TailwindUi.flash/default',
                'params' => ['type' => 'success'],
            ],
        ]);

        $result = $this->Flash->render();
        $this->assertNotNull($result);
        $this->assertStringContainsString('alert', $result);
        $this->assertStringContainsString('alert-success', $result);
        $this->assertStringContainsString('Item saved successfully.', $result);
    }

    public function testRenderError(): void
    {
        $this->Session->write('Flash.flash', [
            [
                'message' => 'Something went wrong.',
                'key' => 'flash',
                'element' => 'TailwindUi.flash/default',
                'params' => ['type' => 'error'],
            ],
        ]);

        $result = $this->Flash->render();
        $this->assertNotNull($result);
        $this->assertStringContainsString('alert-error', $result);
        $this->assertStringContainsString('Something went wrong.', $result);
    }

    public function testRenderClearsSession(): void
    {
        $this->Session->write('Flash.flash', [
            [
                'message' => 'Test.',
                'key' => 'flash',
                'element' => 'TailwindUi.flash/default',
                'params' => ['type' => 'default'],
            ],
        ]);

        $this->Flash->render();
        $this->assertNull($this->Session->read('Flash.flash'));
    }

    public function testRenderEscapesHtml(): void
    {
        $this->Session->write('Flash.flash', [
            [
                'message' => '<script>alert("xss")</script>',
                'key' => 'flash',
                'element' => 'TailwindUi.flash/default',
                'params' => ['type' => 'info'],
            ],
        ]);

        $result = $this->Flash->render();
        $this->assertNotNull($result);
        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    public function testRenderDismissButtonIsCspFriendly(): void
    {
        $this->Session->write('Flash.flash', [
            [
                'message' => 'Item saved.',
                'key' => 'flash',
                'element' => 'TailwindUi.flash/default',
                'params' => ['type' => 'success'],
            ],
        ]);

        $result = $this->Flash->render();
        $this->assertNotNull($result);
        $this->assertStringNotContainsString('onclick', $result);
        $this->assertStringContainsString('data-tailwind-ui-dismiss="[role=alert]"', $result);
    }
}
