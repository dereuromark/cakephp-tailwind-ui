<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper;

use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use TailwindUi\View\Helper\HtmlHelper;

class HtmlHelperTest extends TestCase
{
    protected View $View;

    protected HtmlHelper $Html;

    public function setUp(): void
    {
        parent::setUp();
        Configure::delete('TailwindUi');

        $request = new ServerRequest([
            'webroot' => '',
            'base' => '',
            'url' => '/articles/index',
            'params' => ['controller' => 'Articles', 'action' => 'index', 'plugin' => null],
        ]);
        $this->View = new View($request);
        $this->Html = new HtmlHelper($this->View);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Configure::delete('TailwindUi');
        unset($this->Html, $this->View);
    }

    public function testBadgeDefault(): void
    {
        $result = $this->Html->badge('New');
        $this->assertStringContainsString('badge', $result);
        $this->assertStringContainsString('badge-secondary', $result);
        $this->assertStringContainsString('New', $result);
    }

    public function testBadgeWithVariant(): void
    {
        $result = $this->Html->badge('Success', ['class' => 'success']);
        $this->assertStringContainsString('badge-success', $result);
        $this->assertStringNotContainsString('badge-secondary', $result);
    }

    public function testBadgeOutline(): void
    {
        $result = $this->Html->badge('Outline', ['class' => 'outline']);
        $this->assertStringContainsString('badge-outline', $result);
    }

    public function testBadgeEscapesText(): void
    {
        $result = $this->Html->badge('<b>bold</b>');
        $this->assertStringNotContainsString('<b>', $result);
        $this->assertStringContainsString('&lt;b&gt;', $result);
    }

    public function testIconDefault(): void
    {
        $result = $this->Html->icon('check');
        // Default DaisyUI uses svg tag
        $this->assertStringContainsString('<svg', $result);
        $this->assertStringContainsString('size-5', $result);
    }

    public function testIconWithKtui(): void
    {
        Configure::write('TailwindUi.classMap', 'ktui');
        $this->Html = new HtmlHelper($this->View);

        $result = $this->Html->icon('check');
        $this->assertStringContainsString('<i', $result);
        $this->assertStringContainsString('ki', $result);
        $this->assertStringContainsString('ki-filled', $result);
        $this->assertStringContainsString('check', $result);
    }
}
