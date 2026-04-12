<?php
declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Widget;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\View\StringTemplate;
use TailwindUi\View\Widget\BasicWidget;

class BasicWidgetTest extends TestCase {

	protected StringTemplate $templates;

	/**
	 * @var \Cake\View\Form\ContextInterface&\PHPUnit\Framework\MockObject\MockObject
	 */
	protected $context;

	public function setUp(): void {
		parent::setUp();
		$this->templates = new StringTemplate(['input' => '<input type="{{type}}" name="{{name}}"{{attrs}} />']);
		$this->context = $this->getMockBuilder('Cake\View\Form\ContextInterface')->getMock();
		Configure::delete('TailwindUi');
	}

	public function tearDown(): void {
		parent::tearDown();
		Configure::delete('TailwindUi');
	}

	public function testRenderBasic(): void {
		$widget = new BasicWidget($this->templates);
		$result = $widget->render(['name' => 'email', 'type' => 'email'], $this->context);
		$this->assertStringContainsString('input input-bordered w-full', $result);
		$this->assertStringContainsString('type="email"', $result);
	}

	public function testRenderHiddenNoClasses(): void {
		$widget = new BasicWidget($this->templates);
		$result = $widget->render(['name' => 'id', 'type' => 'hidden'], $this->context);
		$this->assertStringNotContainsString('input-bordered', $result);
	}

	public function testRenderWithPrepend(): void {
		$widget = new BasicWidget($this->templates);
		$result = $widget->render(['name' => 'price', 'type' => 'text', 'prepend' => '$'], $this->context);
		$this->assertStringContainsString('join w-full', $result);
		$this->assertStringContainsString('$', $result);
	}

}
