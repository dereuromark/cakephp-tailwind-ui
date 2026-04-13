<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper\FormHelper;

/**
 * Covers daisyUI 5 floating-label rendering. Uses a different markup
 * structure than the regular fieldset wrapper: the label becomes a
 * <span> inside <label class="floating-label"> wrapping the input.
 */
class FloatingLabelTest extends FormHelperTestCase
{
    public function testFloatingLabelWrapsInputInFloatingLabel(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['floating' => true]);

        $this->assertStringContainsString('<label class="floating-label">', $result);
        $this->assertStringContainsString('<span', $result);
        $this->assertStringContainsString('<input', $result);
        // No fieldset/legend in floating mode.
        $this->assertStringNotContainsString('<fieldset class="fieldset"', $result);
        $this->assertStringNotContainsString('<legend', $result);
    }

    public function testFloatingLabelSetsPlaceholderIfMissing(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['floating' => true]);

        // daisyUI's floating-label needs `:placeholder-shown` to work, so
        // we inject a placeholder if the user didn't provide one.
        $this->assertStringContainsString('placeholder=" "', $result);
    }

    public function testFloatingLabelKeepsCustomPlaceholder(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', [
            'floating' => true,
            'placeholder' => 'Enter title',
        ]);

        $this->assertStringContainsString('placeholder="Enter title"', $result);
    }

    public function testFloatingLabelOnSelect(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('status', [
            'options' => ['a' => 'A'],
            'floating' => true,
        ]);

        $this->assertStringContainsString('<label class="floating-label">', $result);
        $this->assertStringContainsString('<select', $result);
    }

    public function testFloatingLabelOnTextarea(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('body', ['floating' => true]);

        $this->assertStringContainsString('<label class="floating-label">', $result);
        $this->assertStringContainsString('<textarea', $result);
    }

    public function testFloatingLabelIgnoredForCheckbox(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('published', ['floating' => true]);

        // Checkbox doesn't support floating labels — falls back to normal rendering.
        $this->assertStringNotContainsString('floating-label', $result);
        $this->assertStringContainsString('checkbox', $result);
    }

    public function testFloatingLabelWithError(): void
    {
        $this->Form->create([
            'schema' => $this->article['schema'],
            'required' => $this->article['required'],
            'errors' => ['title' => ['_empty' => 'Required']],
        ]);
        $result = $this->Form->control('title', ['floating' => true]);

        $this->assertStringContainsString('floating-label', $result);
        // Error class still applies to the input.
        $this->assertMatchesRegularExpression('/<input[^>]*class="[^"]*validator/', $result);
    }
}
