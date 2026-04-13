<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper\FormHelper;

/**
 * Covers prepend/append input groups — wrapper class, addon rendering,
 * ordering, and the button-addon special case. These had zero coverage
 * before #4.
 */
class InputGroupTest extends FormHelperTestCase
{
    public function testPrependWrapsInJoin(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['prepend' => '$']);

        $this->assertStringContainsString('join w-full', $result);
        $this->assertStringContainsString('<span class="join-item btn btn-ghost no-animation">$</span>', $result);
    }

    public function testAppendWrapsInJoin(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['append' => '.com']);

        $this->assertStringContainsString('join w-full', $result);
        $this->assertStringContainsString('<span class="join-item btn btn-ghost no-animation">.com</span>', $result);
    }

    public function testPrependOrderingIsBeforeInput(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['prepend' => '$']);

        $prependPos = strpos($result, '>$</span>');
        $inputPos = strpos($result, '<input');
        $this->assertIsInt($prependPos);
        $this->assertIsInt($inputPos);
        $this->assertLessThan($inputPos, $prependPos, 'Prepend addon should render before the input.');
    }

    public function testAppendOrderingIsAfterInput(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['append' => '.com']);

        $appendPos = strpos($result, '>.com</span>');
        $inputPos = strpos($result, '<input');
        $this->assertIsInt($appendPos);
        $this->assertIsInt($inputPos);
        $this->assertLessThan($appendPos, $inputPos, 'Append addon should render after the input.');
    }

    public function testBothPrependAndAppend(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['prepend' => '$', 'append' => '.00']);

        $this->assertStringContainsString('>$</span>', $result);
        $this->assertStringContainsString('>.00</span>', $result);
    }

    public function testButtonAddonRendersRaw(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('search', [
            'type' => 'text',
            'append' => '<button type="button" class="btn">Go</button>',
        ]);

        // Button HTML passes through raw, not wrapped in a <span>.
        $this->assertStringContainsString('<button type="button" class="btn">Go</button>', $result);
        $this->assertStringNotContainsString('<span class="join-item btn btn-ghost no-animation"><button', $result);
    }

    public function testSelectWithPrepend(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('status', [
            'type' => 'select',
            'options' => ['a' => 'A'],
            'prepend' => 'Status:',
        ]);

        $this->assertStringContainsString('join w-full', $result);
        $this->assertStringContainsString('<select', $result);
    }

    public function testFileInputDropsPrependAndAppendSilently(): void
    {
        $this->Form->create($this->article);
        $result = $this->Form->control('avatar', [
            'type' => 'file',
            'prepend' => '$',
            'append' => '.png',
        ]);

        // File inputs don't compose with the join wrapper; addons are dropped.
        $this->assertStringNotContainsString('join w-full', $result);
        $this->assertStringNotContainsString('>$</span>', $result);
        $this->assertStringNotContainsString('>.png</span>', $result);
        // The file input itself still renders normally.
        $this->assertStringContainsString('type="file"', $result);
        $this->assertStringContainsString('file-input', $result);
    }
}
