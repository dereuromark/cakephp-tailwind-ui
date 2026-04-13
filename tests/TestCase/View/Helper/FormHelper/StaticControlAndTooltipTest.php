<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper\FormHelper;

/**
 * Covers staticControl(), label tooltips, and tooltip error feedback —
 * three small parity items from #1.
 */
class StaticControlAndTooltipTest extends FormHelperTestCase
{
    public function testStaticControlRendersParagraphAndHidden(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->staticControl('title', ['value' => 'Hello World']);

        $this->assertStringContainsString('<fieldset class="fieldset">', $result);
        $this->assertStringContainsString('<legend', $result);
        $this->assertStringContainsString('<p class="py-2 text-base-content">Hello World</p>', $result);
        $this->assertStringContainsString('type="hidden"', $result);
        $this->assertStringContainsString('name="title"', $result);
    }

    public function testStaticControlLabelFalse(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->staticControl('title', ['value' => 'X', 'label' => false]);

        $this->assertStringNotContainsString('<legend', $result);
        $this->assertStringNotContainsString('<label', $result);
    }

    public function testStaticControlEscapesValue(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->staticControl('title', ['value' => '<b>boom</b>']);

        $this->assertStringNotContainsString('<b>boom</b>', $result);
        $this->assertStringContainsString('&lt;b&gt;boom&lt;/b&gt;', $result);
    }

    public function testStaticControlHorizontalUsesDiv(): void
    {
        $this->Form->create($this->article, ['align' => 'horizontal']);
        $result = $this->Form->staticControl('title', ['value' => 'X']);

        $this->assertStringContainsString('flex items-start gap-4', $result);
        $this->assertStringNotContainsString('<fieldset', $result);
    }

    public function testStaticControlWithHelp(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->staticControl('title', ['value' => 'X', 'help' => 'Read-only']);

        $this->assertStringContainsString('Read-only', $result);
        $this->assertStringContainsString('text-base-content/60', $result);
    }

    public function testLabelTooltipAppendsIconSpan(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['tooltip' => 'Required field']);

        $this->assertStringContainsString('data-tip="Required field"', $result);
        $this->assertStringContainsString('class="tooltip tooltip-right ml-1 align-middle"', $result);
        $this->assertStringContainsString('<svg', $result);
    }

    public function testLabelTooltipEscapesText(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['tooltip' => '<b>bad</b>']);

        $this->assertStringContainsString('data-tip="&lt;b&gt;bad&lt;/b&gt;"', $result);
        $this->assertStringNotContainsString('<b>bad</b>', $result);
    }

    public function testTooltipErrorFeedbackWrapsInput(): void
    {
        $this->Form->create([
            'schema' => $this->article['schema'],
            'required' => $this->article['required'],
            'errors' => ['title' => ['_empty' => 'Required']],
        ]);
        $result = $this->Form->control('title', ['feedbackStyle' => 'tooltip']);

        // Input is wrapped in a tooltip div containing the error text.
        $this->assertStringContainsString('tooltip tooltip-error tooltip-open', $result);
        $this->assertStringContainsString('data-tip="Required"', $result);
        // The regular block error <p> is suppressed.
        $this->assertStringNotContainsString('<p id="title-error"', $result);
    }

    public function testTooltipErrorFeedbackNoErrorIsBlockStyle(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['feedbackStyle' => 'tooltip']);

        // No error → no tooltip wrapper, no suppression.
        $this->assertStringNotContainsString('tooltip-error', $result);
    }
}
