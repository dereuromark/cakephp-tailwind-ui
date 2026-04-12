<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use TailwindUi\Test\TestCase\View\Helper\Stub\ClassMapTraitUser;

class ClassMapTraitTest extends TestCase
{
    protected ClassMapTraitUser $subject;

    public function setUp(): void
    {
        parent::setUp();
        Configure::delete('TailwindUi');
        $this->subject = new ClassMapTraitUser(new View());
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Configure::delete('TailwindUi');
    }

    public function testDefaultDaisyuiMap(): void
    {
        $this->assertSame('input input-bordered w-full', $this->subject->get('form.input'));
        $this->assertSame('btn', $this->subject->get('btn'));
        $this->assertSame('alert', $this->subject->get('alert'));
    }

    public function testUnknownKeyReturnsEmpty(): void
    {
        $this->assertSame('', $this->subject->get('nonexistent.key'));
    }

    public function testPresetSwap(): void
    {
        Configure::write('TailwindUi.classMap', 'ktui');
        $this->subject->reset();
        $this->assertSame('kt-input', $this->subject->get('form.input'));
        $this->assertSame('kt-btn', $this->subject->get('btn'));
    }

    public function testArrayOverrides(): void
    {
        Configure::write('TailwindUi.classMap', ['form.input' => 'custom-input']);
        $this->subject->reset();
        $this->assertSame('custom-input', $this->subject->get('form.input'));
        $this->assertSame('btn', $this->subject->get('btn'));
    }

    public function testPresetWithOverrides(): void
    {
        Configure::write('TailwindUi.classMap', 'ktui');
        Configure::write('TailwindUi.classMapOverrides', ['form.input' => 'kt-input custom-extra']);
        $this->subject->reset();
        $this->assertSame('kt-input custom-extra', $this->subject->get('form.input'));
        $this->assertSame('kt-btn', $this->subject->get('btn'));
    }
}
