<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper\FormHelper;

/**
 * Covers the inline form layout mode for search/filter bars.
 */
class InlineFormHelperTest extends FormHelperTestCase
{
    public function testCreateInlineWrapsInFlexRow(): void
    {
        $result = $this->Form->create($this->article, ['align' => 'inline']);

        $this->assertStringContainsString('<form', $result);
        $this->assertStringContainsString('<div class="flex flex-wrap items-end gap-3 mb-4">', $result);
    }

    public function testEndInlineClosesWrapper(): void
    {
        $this->Form->create($this->article, ['align' => 'inline']);
        $result = $this->Form->end();

        $this->assertStringContainsString('</div>', $result);
        $this->assertStringContainsString('</form>', $result);
        // Wrapper div closes before the form tag.
        $this->assertLessThan(
            strpos($result, '</form>'),
            strpos($result, '</div>'),
            'Wrapper div should close before the form tag.',
        );
    }

    public function testInlineControlUsesSrOnlyLabel(): void
    {
        $this->Form->create($this->article, ['align' => 'inline']);
        $result = $this->Form->control('title');

        $this->assertStringContainsString('class="sr-only"', $result);
        // No fieldset in inline mode — flat div wrapper.
        $this->assertStringNotContainsString('<fieldset class="fieldset"', $result);
    }

    public function testInlineModeSuppressesHelpText(): void
    {
        $this->Form->create($this->article, ['align' => 'inline']);
        $result = $this->Form->control('title', ['help' => 'Search term']);

        $this->assertStringNotContainsString('Search term', $result);
    }

    public function testInlineSubmitHasNoMb4Container(): void
    {
        $this->Form->create($this->article, ['align' => 'inline']);
        $result = $this->Form->submit('Search');

        // Submit container in inline mode is empty, not `mb-4`.
        $this->assertStringNotContainsString('<div class="mb-4"', $result);
        $this->assertStringContainsString('btn', $result);
    }

    public function testInlineModeResetsAfterEnd(): void
    {
        $this->Form->create($this->article, ['align' => 'inline']);
        $this->Form->end();

        $this->Form->create($this->article);
        $result = $this->Form->control('title');

        // Back to fieldset default.
        $this->assertStringContainsString('<fieldset class="fieldset"', $result);
        $this->assertStringNotContainsString('class="sr-only"', $result);
    }
}
