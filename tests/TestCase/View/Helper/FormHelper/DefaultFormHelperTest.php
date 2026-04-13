<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper\FormHelper;

use Cake\Core\Configure;
use TailwindUi\View\Helper\FormHelper;

class DefaultFormHelperTest extends FormHelperTestCase
{
    public function testTextControl(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title');
        $this->assertStringContainsString('input w-full', $result);
        $this->assertStringContainsString('fieldset-legend', $result);
        $this->assertStringContainsString('<fieldset class="fieldset"', $result);
        $this->assertStringContainsString('type="text"', $result);
        $this->assertStringContainsString('>Title</legend>', $result);
        $this->assertStringNotContainsString('for="title"', $result);
    }

    public function testSelectControl(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('status', ['options' => ['a' => 'Active', 'i' => 'Inactive']]);
        $this->assertStringContainsString('select w-full', $result);
        $this->assertStringContainsString('<fieldset class="fieldset"', $result);
    }

    public function testCheckboxControl(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('published');
        $this->assertStringContainsString('checkbox', $result);
        $this->assertStringContainsString('cursor-pointer', $result);
        $this->assertStringContainsString(' Published</label>', $result);
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
        $this->assertStringContainsString('textarea w-full', $result);
        $this->assertStringContainsString('<fieldset class="fieldset"', $result);
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

    public function testSubmitWithGhostModifier(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->submit('Cancel', ['class' => 'ghost']);
        $this->assertStringContainsString('btn-ghost', $result);
        // ghost is a modifier, so the primary default still applies
        $this->assertStringContainsString('btn-primary', $result);
    }

    public function testSubmitWithSoftColor(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->submit('Soft', ['class' => 'soft danger']);
        $this->assertStringContainsString('btn-soft', $result);
        $this->assertStringContainsString('btn-error', $result);
        $this->assertStringNotContainsString('btn-primary', $result);
    }

    public function testHelpText(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['help' => 'Enter your title here']);
        $this->assertStringContainsString('Enter your title here', $result);
        $this->assertStringContainsString('<p id="title-help"', $result);
        $this->assertStringContainsString('class="label text-base-content/60"', $result);
        $this->assertStringContainsString('aria-describedby', $result);
    }

    public function testHiddenFieldNoClasses(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('id');
        $this->assertStringNotContainsString('class="input', $result);
        $this->assertStringNotContainsString('<fieldset', $result);
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
