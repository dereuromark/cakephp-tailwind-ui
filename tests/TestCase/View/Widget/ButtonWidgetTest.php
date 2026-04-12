<?php
declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Widget;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\View\StringTemplate;
use TailwindUi\View\Widget\ButtonWidget;

class ButtonWidgetTest extends TestCase
{
    protected StringTemplate $templates;
    protected $context;

    public function setUp(): void
    {
        parent::setUp();
        $this->templates = new StringTemplate(['button' => '<button{{attrs}}>{{text}}</button>']);
        $this->context = $this->getMockBuilder('Cake\View\Form\ContextInterface')->getMock();
        Configure::delete('TailwindUi');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Configure::delete('TailwindUi');
        // Reset the static cache in InputGroupTrait-like static, not needed here
    }

    public function testRenderDefaultPrimary(): void
    {
        $button = new ButtonWidget($this->templates);
        $result = $button->render(['name' => 'submit', 'text' => 'Save'], $this->context);
        $this->assertStringContainsString('btn', $result);
        $this->assertStringContainsString('btn-primary', $result);
    }

    public function testRenderWithVariant(): void
    {
        $button = new ButtonWidget($this->templates);
        $result = $button->render(['name' => 'delete', 'text' => 'Delete', 'class' => 'danger'], $this->context);
        $this->assertStringContainsString('btn', $result);
        $this->assertStringContainsString('btn-error', $result);
        $this->assertStringNotContainsString(' danger', $result);
    }

    public function testRenderWithKtui(): void
    {
        Configure::write('TailwindUi.classMap', 'ktui');
        $button = new ButtonWidget($this->templates);
        $result = $button->render(['name' => 'submit', 'text' => 'Save'], $this->context);
        $this->assertStringContainsString('kt-btn', $result);
        $this->assertStringContainsString('kt-btn-primary', $result);
    }
}
