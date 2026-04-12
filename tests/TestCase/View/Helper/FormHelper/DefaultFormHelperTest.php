<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper\FormHelper;

use Cake\Core\Configure;
use TailwindUi\View\Helper\FormHelper;

class DefaultFormHelperTest extends AbstractFormHelperTest
{
    public function testTextControl(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title');
        $this->assertStringContainsString('input input-bordered w-full', $result);
        $this->assertStringContainsString('label-text', $result);
        $this->assertStringContainsString('mb-4', $result);
        $this->assertStringContainsString('type="text"', $result);
    }

    public function testSelectControl(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('status', ['options' => ['a' => 'Active', 'i' => 'Inactive']]);
        $this->assertStringContainsString('select select-bordered w-full', $result);
    }

    public function testCheckboxControl(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('published');
        $this->assertStringContainsString('checkbox', $result);
        $this->assertStringContainsString('cursor-pointer', $result);
    }

    public function testSwitchCheckbox(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('published', ['switch' => true]);
        $this->assertStringContainsString('toggle', $result);
    }

    public function testTextareaControl(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('body');
        $this->assertStringContainsString('textarea textarea-bordered w-full', $result);
    }

    public function testSubmitButton(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->submit('Save');
        $this->assertStringContainsString('btn', $result);
        $this->assertStringContainsString('btn-primary', $result);
    }

    public function testSubmitWithVariant(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->submit('Delete', ['class' => 'danger']);
        $this->assertStringContainsString('btn', $result);
        $this->assertStringContainsString('btn-error', $result);
    }

    public function testHelpText(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['help' => 'Enter your title here']);
        $this->assertStringContainsString('Enter your title here', $result);
        $this->assertStringContainsString('label-text-alt', $result);
        $this->assertStringContainsString('aria-describedby', $result);
    }

    public function testHiddenFieldNoClasses(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('id');
        $this->assertStringNotContainsString('input-bordered', $result);
    }

    public function testLabelFalse(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['label' => false]);
        $this->assertStringNotContainsString('<label', $result);
    }

    public function testCustomLabelText(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['label' => 'My Custom Label']);
        $this->assertStringContainsString('My Custom Label', $result);
    }

    public function testKtuiClassMap(): void
    {
        Configure::write('TailwindUi.classMap', 'ktui');
        $this->Form = new FormHelper($this->View);
        $this->Form->create($this->article);
        $result = $this->Form->control('title');
        $this->assertStringContainsString('kt-input', $result);
        $this->assertStringContainsString('kt-form-label', $result);
    }
}
