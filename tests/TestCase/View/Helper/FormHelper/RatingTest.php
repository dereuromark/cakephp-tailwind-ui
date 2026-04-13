<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper\FormHelper;

/**
 * Covers FormHelper::rating() — daisyUI 5 rating component as a styled
 * radio group wrapped in the standard fieldset/horizontal container.
 */
class RatingTest extends FormHelperTestCase
{
    public function testRatingRendersFiveStarsByDefault(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->rating('quality');

        $this->assertStringContainsString('<fieldset class="fieldset">', $result);
        $this->assertStringContainsString('<div class="rating">', $result);
        // 5 visible stars + 1 hidden "no rating" radio = 6 inputs.
        $this->assertSame(6, substr_count($result, '<input type="radio"'));
        $this->assertStringContainsString('mask mask-star-2 bg-orange-400', $result);
    }

    public function testRatingMaxOption(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->rating('quality', ['max' => 10, 'allowEmpty' => false]);

        $this->assertSame(10, substr_count($result, '<input type="radio"'));
    }

    public function testRatingValueOptionMarksCorrectStar(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->rating('quality', ['value' => 3]);

        $this->assertStringContainsString('value="3" id="quality-3" class="mask mask-star-2 bg-orange-400" checked="checked"', $result);
    }

    public function testRatingZeroValueChecksHiddenInput(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->rating('quality', ['value' => 0]);

        $this->assertStringContainsString('value="0"', $result);
        $this->assertStringContainsString('rating-hidden', $result);
        $this->assertMatchesRegularExpression('/value="0"[^>]*checked="checked"/', $result);
    }

    public function testRatingNullValueDefaultsToHidden(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->rating('quality', ['value' => null]);

        // Null falls back to the empty radio being checked.
        $this->assertMatchesRegularExpression('/value="0"[^>]*checked="checked"/', $result);
    }

    public function testRatingAllowEmptyFalseOmitsHiddenInput(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->rating('quality', ['allowEmpty' => false]);

        $this->assertStringNotContainsString('rating-hidden', $result);
        $this->assertSame(5, substr_count($result, '<input type="radio"'));
    }

    public function testRatingSizeOption(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->rating('quality', ['size' => 'lg']);

        $this->assertStringContainsString('class="rating rating-lg"', $result);
    }

    public function testRatingHorizontalLayout(): void
    {
        $this->Form->create($this->article, ['align' => 'horizontal']);
        $result = $this->Form->rating('quality');

        $this->assertStringContainsString('flex items-start gap-4', $result);
        $this->assertStringNotContainsString('<fieldset', $result);
    }

    public function testRatingLabelFalse(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->rating('quality', ['label' => false]);

        $this->assertStringNotContainsString('<legend', $result);
    }

    public function testRatingHelpText(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->rating('quality', ['help' => 'Pick a star']);

        $this->assertStringContainsString('Pick a star', $result);
        $this->assertStringContainsString('text-base-content/60', $result);
    }

    public function testRatingAriaLabels(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->rating('quality');

        $this->assertStringContainsString('aria-label="1 star"', $result);
        $this->assertStringContainsString('aria-label="2 stars"', $result);
        $this->assertStringContainsString('aria-label="No rating"', $result);
    }
}
