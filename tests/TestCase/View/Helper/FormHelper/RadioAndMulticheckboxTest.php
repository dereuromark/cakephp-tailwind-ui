<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper\FormHelper;

/**
 * Covers radio and multicheckbox controls — group wrappers, aria plumbing,
 * per-item widget classes, and horizontal-mode edge cases. These control
 * paths had zero test coverage before #4.
 */
class RadioAndMulticheckboxTest extends FormHelperTestCase
{
    public function testRadioGroupWrapsInFieldset(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('status', [
            'type' => 'radio',
            'options' => ['a' => 'Active', 'i' => 'Inactive'],
        ]);

        $this->assertStringContainsString('<fieldset', $result);
        $this->assertStringContainsString('role="group"', $result);
        $this->assertStringContainsString('aria-labelledby="status-label"', $result);
    }

    public function testRadioItemsHaveRadioClass(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('status', [
            'type' => 'radio',
            'options' => ['a' => 'Active', 'i' => 'Inactive'],
        ]);

        // Each radio input gets the daisyUI `radio` class from form.radio.
        $this->assertSame(2, substr_count($result, 'class="radio"'));
        $this->assertStringContainsString('type="radio"', $result);
    }

    public function testMulticheckboxWrapsInFieldset(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('tags._ids', [
            'type' => 'select',
            'multiple' => 'checkbox',
            'options' => ['1' => 'PHP', '2' => 'JS'],
        ]);

        $this->assertStringContainsString('<fieldset', $result);
        $this->assertStringContainsString('role="group"', $result);
        $this->assertStringContainsString('aria-labelledby="tags-ids-label"', $result);
        $this->assertStringContainsString('type="checkbox"', $result);
    }

    public function testMulticheckboxItemsHaveCheckboxClass(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('tags._ids', [
            'type' => 'multicheckbox',
            'options' => ['1' => 'PHP', '2' => 'JS'],
        ]);

        $this->assertStringContainsString('class="checkbox"', $result);
    }

    public function testRadioInHorizontalMode(): void
    {
        $this->Form->create($this->article, ['align' => 'horizontal']);
        $result = $this->Form->control('status', [
            'type' => 'radio',
            'options' => ['a' => 'Active', 'i' => 'Inactive'],
        ]);

        // Horizontal keeps div wrapper, no fieldset.
        $this->assertStringNotContainsString('<fieldset', $result);
        $this->assertStringContainsString('flex items-start gap-4', $result);
        $this->assertStringContainsString('class="radio"', $result);
    }

    public function testMulticheckboxInHorizontalMode(): void
    {
        $this->Form->create($this->article, ['align' => 'horizontal']);
        $result = $this->Form->control('tags._ids', [
            'type' => 'multicheckbox',
            'options' => ['1' => 'PHP', '2' => 'JS'],
        ]);

        $this->assertStringNotContainsString('<fieldset class="fieldset"', $result);
        $this->assertStringContainsString('flex items-start gap-4', $result);
    }

    public function testRadioLabelFalseOmitsLegend(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('status', [
            'type' => 'radio',
            'options' => ['a' => 'Active', 'i' => 'Inactive'],
            'label' => false,
        ]);

        $this->assertStringContainsString('<fieldset', $result);
        $this->assertStringNotContainsString('<legend', $result);
    }
}
