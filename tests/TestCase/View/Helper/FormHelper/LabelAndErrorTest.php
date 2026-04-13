<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper\FormHelper;

/**
 * Covers label=false across layout modes, custom label arrays, and
 * per-field-type validation error class injection. These edge cases
 * had zero coverage before #4.
 */
class LabelAndErrorTest extends FormHelperTestCase
{
    public function testLabelFalseInVerticalMode(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['label' => false]);

        $this->assertStringContainsString('<fieldset class="fieldset">', $result);
        $this->assertStringNotContainsString('<legend', $result);
        $this->assertStringNotContainsString('<label', $result);
    }

    public function testLabelFalseInHorizontalMode(): void
    {
        $this->Form->create($this->article, ['align' => 'horizontal']);
        $result = $this->Form->control('title', ['label' => false]);

        $this->assertStringContainsString('flex items-start gap-4', $result);
        $this->assertStringNotContainsString('<label', $result);
    }

    public function testCustomLabelArrayMergesClass(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', [
            'label' => ['text' => 'My Title', 'class' => 'extra-class'],
        ]);

        $this->assertStringContainsString('My Title', $result);
        // Both the user's class and our default legend class should be present.
        $this->assertStringContainsString('extra-class', $result);
        $this->assertStringContainsString('fieldset-legend', $result);
    }

    public function testCustomLabelStringReplacesText(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['label' => 'My Custom Title']);

        $this->assertStringContainsString('My Custom Title', $result);
        $this->assertStringContainsString('fieldset-legend', $result);
    }

    public function testTextFieldErrorAddsValidatorClass(): void
    {
        $this->Form->create([
            'schema' => $this->article['schema'],
            'required' => $this->article['required'],
            'errors' => ['title' => ['_empty' => 'Title required']],
        ]);
        $result = $this->Form->control('title');

        $this->assertStringContainsString('Title required', $result);
        // form.inputError → `validator` class gets injected on the input.
        $this->assertMatchesRegularExpression('/<input[^>]*class="[^"]*validator/', $result);
    }

    public function testSelectErrorAddsValidatorClass(): void
    {
        $this->Form->create([
            'schema' => $this->article['schema'],
            'required' => $this->article['required'],
            'errors' => ['status' => ['_empty' => 'Status required']],
        ]);
        $result = $this->Form->control('status', ['options' => ['a' => 'Active']]);

        $this->assertStringContainsString('Status required', $result);
        $this->assertMatchesRegularExpression('/<select[^>]*class="[^"]*validator/', $result);
    }

    public function testTextareaErrorAddsValidatorClass(): void
    {
        $this->Form->create([
            'schema' => $this->article['schema'],
            'required' => $this->article['required'],
            'errors' => ['body' => ['_empty' => 'Body required']],
        ]);
        $result = $this->Form->control('body');

        $this->assertStringContainsString('Body required', $result);
        $this->assertMatchesRegularExpression('/<textarea[^>]*class="[^"]*validator/', $result);
    }

    public function testErrorMessageUsesFormErrorClass(): void
    {
        $this->Form->create([
            'schema' => $this->article['schema'],
            'required' => $this->article['required'],
            'errors' => ['title' => ['_empty' => 'Title required']],
        ]);
        $result = $this->Form->control('title');

        // The error message renders as `<p class="label text-error">` (daisyUI 5 idiom).
        $this->assertMatchesRegularExpression('/<p[^>]*class="label text-error"[^>]*>Title required<\/p>/', $result);
    }
}
