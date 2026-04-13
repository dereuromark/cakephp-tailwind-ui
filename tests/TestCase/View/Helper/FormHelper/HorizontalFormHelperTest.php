<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper\FormHelper;

class HorizontalFormHelperTest extends FormHelperTestCase
{
    public function testHorizontalTextControl(): void
    {
        $this->Form->create($this->article, ['align' => 'horizontal']);
        $result = $this->Form->control('title');
        $this->assertStringContainsString('flex items-start gap-4', $result);
        $this->assertStringContainsString('w-48 pt-2 shrink-0 text-right', $result);
    }

    public function testHorizontalSelect(): void
    {
        $this->Form->create($this->article, ['align' => 'horizontal']);
        $result = $this->Form->control('status', ['options' => ['a' => 'Active', 'i' => 'Inactive']]);
        $this->assertStringContainsString('flex items-start gap-4', $result);
        $this->assertStringContainsString('select w-full', $result);
        // Horizontal mode keeps the div wrapper — no fieldset.
        $this->assertStringNotContainsString('<fieldset', $result);
    }

    public function testAlignResetAfterEnd(): void
    {
        $this->Form->create($this->article, ['align' => 'horizontal']);
        $this->Form->end();

        $this->Form->create($this->article);
        $result = $this->Form->control('title');
        // Default alignment uses fieldset wrapper (not the mb-4 div).
        $this->assertStringContainsString('<fieldset class="fieldset"', $result);
        $this->assertStringNotContainsString('flex items-start gap-4', $result);
    }
}
