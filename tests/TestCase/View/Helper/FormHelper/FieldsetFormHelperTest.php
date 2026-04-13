<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper\FormHelper;

use Cake\Core\Configure;
use TailwindUi\View\Helper\FormHelper;

/**
 * Tests the daisyUI 5 fieldset-based control rendering, including the
 * preset-contributed template overrides and the div-based KTUI fallback.
 */
class FieldsetFormHelperTest extends FormHelperTestCase
{
    public function testTextInputWrappedInFieldsetWithLegend(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title');

        $this->assertStringContainsString('<fieldset class="fieldset">', $result);
        $this->assertStringContainsString('<legend class="fieldset-legend"', $result);
        $this->assertStringContainsString('</fieldset>', $result);
        $this->assertStringContainsString('input w-full', $result);
    }

    public function testHelpRendersAsDaisyuiLabelParagraph(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['help' => 'Helper text']);

        $this->assertStringContainsString('<p id="title-help" class="label text-base-content/60">Helper text</p>', $result);
        $this->assertStringContainsString('aria-describedby="title-help"', $result);
    }

    public function testLabelFalseOmitsLegend(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['label' => false]);

        $this->assertStringContainsString('<fieldset class="fieldset">', $result);
        $this->assertStringNotContainsString('<legend', $result);
    }

    public function testSizeOptionInjectsInputSizeClass(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['size' => 'lg']);
        $this->assertStringContainsString('input-lg', $result);

        $result = $this->Form->control('status', [
            'options' => ['a' => 'A'],
            'size' => 'sm',
        ]);
        $this->assertStringContainsString('select-sm', $result);

        $result = $this->Form->control('body', ['size' => 'xl']);
        $this->assertStringContainsString('textarea-xl', $result);
    }

    public function testValidationErrorAddsValidatorClass(): void
    {
        $this->Form->create([
            'schema' => $this->article['schema'],
            'required' => $this->article['required'],
            'errors' => ['title' => ['_empty' => 'Title is required']],
        ]);
        $result = $this->Form->control('title');

        $this->assertStringContainsString('validator', $result);
        $this->assertStringContainsString('<fieldset class="fieldset">', $result);
    }

    public function testSingleCheckboxKeepsInlineFlexWrapper(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('published');

        // Single checkboxes are NOT wrapped in a fieldset; they keep the
        // inline-flex label wrapper from daisyUI.
        $this->assertStringNotContainsString('<fieldset', $result);
        $this->assertStringContainsString('inline-flex', $result);
        $this->assertStringContainsString('checkbox', $result);
    }

    public function testSubmitButtonHasNoFieldsetWrapper(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->submit('Save');

        $this->assertStringNotContainsString('<fieldset', $result);
        $this->assertStringContainsString('btn', $result);
        $this->assertStringContainsString('btn-primary', $result);
    }

    public function testHiddenFieldsNotWrapped(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('id');

        $this->assertStringNotContainsString('<fieldset', $result);
        $this->assertStringContainsString('type="hidden"', $result);
    }

    public function testKtuiPresetKeepsDivWrapper(): void
    {
        Configure::write('TailwindUi.classMap', 'ktui');
        $this->Form = new FormHelper($this->View);
        $this->Form->create($this->article);
        $result = $this->Form->control('title');

        // KTUI's preset templates override back to the div wrapper.
        $this->assertStringContainsString('<div class="mb-4">', $result);
        $this->assertStringNotContainsString('<fieldset class="fieldset"', $result);
        $this->assertStringContainsString('kt-input', $result);
    }

    public function testKtuiHelpRendersAsDiv(): void
    {
        Configure::write('TailwindUi.classMap', 'ktui');
        $this->Form = new FormHelper($this->View);
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['help' => 'Helper text']);

        $this->assertStringContainsString('<div id="title-help"', $result);
        $this->assertStringNotContainsString('<p id="title-help"', $result);
    }
}
