<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper\FormHelper;

/**
 * Covers the daisyUI 5 size/color variants for checkbox, radio, switch
 * and file inputs — the non-text-input half of `['size' => 'lg']` handling,
 * plus `['color' => 'primary']` for switches and file inputs.
 */
class FormVariantsTest extends FormHelperTestCase
{
    public function testCheckboxSize(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('published', ['size' => 'lg']);

        $this->assertStringContainsString('checkbox-lg', $result);
        $this->assertStringContainsString('checkbox', $result);
    }

    public function testRadioSize(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('status', [
            'type' => 'radio',
            'options' => ['a' => 'A', 'b' => 'B'],
            'size' => 'sm',
        ]);

        $this->assertStringContainsString('radio-sm', $result);
    }

    public function testSwitchSize(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('published', [
            'switch' => true,
            'size' => 'xl',
        ]);

        $this->assertStringContainsString('toggle-xl', $result);
        $this->assertStringContainsString('toggle', $result);
    }

    public function testSwitchColor(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('published', [
            'switch' => true,
            'color' => 'primary',
        ]);

        $this->assertStringContainsString('toggle-primary', $result);
    }

    public function testSwitchSizeAndColor(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('published', [
            'switch' => true,
            'size' => 'lg',
            'color' => 'success',
        ]);

        $this->assertStringContainsString('toggle-lg', $result);
        $this->assertStringContainsString('toggle-success', $result);
    }

    public function testSwitchDangerMapsToToggleError(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('published', [
            'switch' => true,
            'color' => 'danger',
        ]);

        // Semantic `danger` → daisyUI `toggle-error`.
        $this->assertStringContainsString('toggle-error', $result);
    }

    public function testFileSize(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('avatar', ['type' => 'file', 'size' => 'lg']);

        $this->assertStringContainsString('file-input-lg', $result);
    }

    public function testFileColorAndGhost(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('avatar', [
            'type' => 'file',
            'color' => 'primary',
        ]);

        $this->assertStringContainsString('file-input-primary', $result);

        $result = $this->Form->control('avatar', [
            'type' => 'file',
            'color' => 'ghost',
        ]);
        $this->assertStringContainsString('file-input-ghost', $result);
    }

    public function testUnknownSizeIsSilentNoop(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('published', ['size' => 'gigantic']);

        // 'gigantic' has no mapping → no class injected, but the control
        // still renders normally.
        $this->assertStringContainsString('checkbox', $result);
        $this->assertStringNotContainsString('gigantic', $result);
    }

    public function testHorizontalCheckboxSize(): void
    {
        $this->Form->create($this->article, ['align' => 'horizontal']);
        $result = $this->Form->control('published', ['size' => 'sm']);

        $this->assertStringContainsString('checkbox-sm', $result);
        $this->assertStringContainsString('flex items-start gap-4', $result);
    }
}
