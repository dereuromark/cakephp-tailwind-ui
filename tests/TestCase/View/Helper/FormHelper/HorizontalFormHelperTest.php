<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper\FormHelper;

class HorizontalFormHelperTest extends AbstractFormHelperTest
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
        $this->assertStringContainsString('select select-bordered w-full', $result);
    }

    public function testAlignResetAfterEnd(): void
    {
        $this->Form->create($this->article, ['align' => 'horizontal']);
        $this->Form->end();

        $this->Form->create($this->article);
        $result = $this->Form->control('title');
        $this->assertStringContainsString('mb-4', $result);
        $this->assertStringNotContainsString('flex items-start gap-4', $result);
    }
}
