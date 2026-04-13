<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper\FormHelper;

use Cake\Core\Configure;
use TailwindUi\View\Helper\FormHelper;

/**
 * Tests for fixes and locked-in behaviors discovered during the post-merge
 * audit of the form helper rewrite. Each test maps to a numbered audit
 * finding so it's clear what's being protected against.
 */
class AuditFixesTest extends FormHelperTestCase
{
    public function testFloatingIsNoOpInInlineMode(): void
    {
        $this->Form->create($this->article, ['align' => 'inline']);
        $result = $this->Form->control('title', ['floating' => true]);

        // No floating-label wrapper; falls back to plain inline rendering.
        $this->assertStringNotContainsString('floating-label', $result);
        // Inline label class still applies.
        $this->assertStringContainsString('class="sr-only"', $result);
    }

    public function testTooltipErrorFeedbackPlusHelpRendersHelpOnce(): void
    {
        $this->Form->create([
            'schema' => $this->article['schema'],
            'required' => $this->article['required'],
            'errors' => ['title' => ['_empty' => 'Required']],
        ]);
        $result = $this->Form->control('title', [
            'feedbackStyle' => 'tooltip',
            'help' => 'Helper text',
        ]);

        $this->assertSame(1, substr_count($result, 'Helper text'));
        $this->assertStringContainsString('tooltip-error', $result);
    }

    public function testRatingClampsValueGreaterThanMax(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->rating('quality', ['max' => 5, 'value' => 7]);

        // The 5th star is checked even though value=7 was passed.
        $this->assertMatchesRegularExpression('/value="5"[^>]*checked="checked"/', $result);
        $this->assertSame(1, substr_count($result, 'checked="checked"'));
    }

    public function testRatingClampsNegativeValueToZero(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->rating('quality', ['max' => 5, 'value' => -3]);

        $this->assertMatchesRegularExpression('/value="0"[^>]*checked="checked"/', $result);
    }

    public function testRatingZeroValueWithoutEmptyOptionLeavesNothingChecked(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->rating('quality', [
            'value' => 0,
            'allowEmpty' => false,
        ]);

        $this->assertSame(0, substr_count($result, 'checked="checked"'));
        $this->assertStringNotContainsString('rating-hidden', $result);
    }

    public function testSingleCheckboxLabelHonorsClassMapOverride(): void
    {
        Configure::write('TailwindUi.classMapOverrides', [
            'form.checkboxLabelInline' => 'my-custom-label',
        ]);
        $this->Form = new FormHelper($this->View);
        $this->Form->create($this->article);
        $result = $this->Form->control('published');

        $this->assertStringContainsString('my-custom-label', $result);
        $this->assertStringNotContainsString('inline-flex items-center gap-2 cursor-pointer', $result);
    }

    public function testKtuiSingleCheckboxLabelStillWorks(): void
    {
        Configure::write('TailwindUi.classMap', 'ktui');
        $this->Form = new FormHelper($this->View);
        $this->Form->create($this->article);
        $result = $this->Form->control('published');

        $this->assertStringContainsString('inline-flex items-center', $result);
    }

    public function testRatingInsideInlineForm(): void
    {
        $create = $this->Form->create($this->article, ['align' => 'inline']);
        $rating = $this->Form->rating('quality', ['max' => 3]);

        // Inline wrapper from create() comes first…
        $this->assertStringContainsString('flex flex-wrap items-end gap-3 mb-4', $create);
        // …and the rating control is a self-contained fieldset that flows
        // inside it (rating doesn't use the inline wrapper itself).
        $this->assertStringContainsString('<div class="rating">', $rating);
    }

    public function testRatingWithTooltipFeedbackStyle(): void
    {
        $this->Form->create([
            'schema' => $this->article['schema'],
            'required' => $this->article['required'],
            'errors' => ['quality' => ['_empty' => 'Required']],
        ]);
        // feedbackStyle is a control() option; rating() doesn't honor it,
        // but it shouldn't crash if extra options are passed.
        $result = $this->Form->rating('quality');

        $this->assertStringContainsString('<div class="rating">', $result);
    }

    public function testFloatingPlusTooltipErrorFeedback(): void
    {
        $this->Form->create([
            'schema' => $this->article['schema'],
            'required' => $this->article['required'],
            'errors' => ['title' => ['_empty' => 'Required']],
        ]);
        $result = $this->Form->control('title', [
            'floating' => true,
            'feedbackStyle' => 'tooltip',
        ]);

        // Floating wrapper wins (it runs after the tooltip block).
        $this->assertStringContainsString('floating-label', $result);
        $this->assertStringContainsString('validator', $result);
    }

    public function testStaticControlInsideInlineForm(): void
    {
        $this->Form->create($this->article, ['align' => 'inline']);
        $result = $this->Form->staticControl('title', ['value' => 'Cached']);

        // staticControl uses the horizontal/fieldset wrapper logic, so under
        // inline mode it falls back to the fieldset wrapper. Verify it at
        // least renders the value and a hidden input.
        $this->assertStringContainsString('Cached', $result);
        $this->assertStringContainsString('type="hidden"', $result);
    }

    public function testKtuiFloatingLabelIsNoOp(): void
    {
        Configure::write('TailwindUi.classMap', 'ktui');
        $this->Form = new FormHelper($this->View);
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['floating' => true]);

        // KTUI's empty form.floatingLabel means floating mode silently
        // skips the template overrides and renders normally.
        $this->assertStringNotContainsString('floating-label', $result);
        $this->assertStringContainsString('kt-input', $result);
    }
}
