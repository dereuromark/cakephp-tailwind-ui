<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper;

use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use TailwindUi\View\Helper\HtmlHelper;

class HtmlHelperTest extends TestCase
{
    protected View $View;

    protected HtmlHelper $Html;

    public function setUp(): void
    {
        parent::setUp();
        Configure::delete('TailwindUi');

        $request = new ServerRequest([
            'webroot' => '',
            'base' => '',
            'url' => '/articles/index',
            'params' => ['controller' => 'Articles', 'action' => 'index', 'plugin' => null],
        ]);
        $this->View = new View($request);
        $this->Html = new HtmlHelper($this->View);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Configure::delete('TailwindUi');
        unset($this->Html, $this->View);
    }

    public function testBadgeDefault(): void
    {
        $result = $this->Html->badge('New');
        $this->assertStringContainsString('badge', $result);
        $this->assertStringContainsString('badge-secondary', $result);
        $this->assertStringContainsString('New', $result);
    }

    public function testBadgeWithVariant(): void
    {
        $result = $this->Html->badge('Success', ['class' => 'success']);
        $this->assertStringContainsString('badge-success', $result);
        $this->assertStringNotContainsString('badge-secondary', $result);
    }

    public function testBadgeOutline(): void
    {
        $result = $this->Html->badge('Outline', ['class' => 'outline']);
        $this->assertStringContainsString('badge-outline', $result);
    }

    public function testBadgeGhost(): void
    {
        $result = $this->Html->badge('Ghost', ['class' => 'ghost']);
        $this->assertStringContainsString('badge-ghost', $result);
        // ghost is a modifier, not a color, so the secondary default still applies
        $this->assertStringContainsString('badge-secondary', $result);
        $this->assertStringNotContainsString('class="badge ghost', $result);
    }

    public function testBadgeSoftWithColor(): void
    {
        $result = $this->Html->badge('Soft', ['class' => 'soft primary']);
        $this->assertStringContainsString('badge-soft', $result);
        $this->assertStringContainsString('badge-primary', $result);
        $this->assertStringNotContainsString('badge-secondary', $result);
        $this->assertStringNotContainsString('"soft', $result);
    }

    public function testBadgeNeutralIsColor(): void
    {
        $result = $this->Html->badge('N', ['class' => 'neutral']);
        $this->assertStringContainsString('badge-neutral', $result);
        // neutral counts as a color, so no secondary default
        $this->assertStringNotContainsString('badge-secondary', $result);
    }

    public function testBadgeSizeVariants(): void
    {
        $result = $this->Html->badge('XS', ['class' => 'xs']);
        $this->assertStringContainsString('badge-xs', $result);

        $result = $this->Html->badge('XL', ['class' => 'primary xl']);
        $this->assertStringContainsString('badge-xl', $result);
        $this->assertStringContainsString('badge-primary', $result);
    }

    public function testBadgeCustomVariantViaOverride(): void
    {
        Configure::write('TailwindUi.classMapOverrides', [
            'badge.host' => 'badge-host',
        ]);
        $this->Html = new HtmlHelper($this->View);

        $result = $this->Html->badge('Host', ['class' => 'host']);
        $this->assertStringContainsString('badge-host', $result);
        // host is not a color, so default secondary still applies
        $this->assertStringContainsString('badge-secondary', $result);
        $this->assertStringNotContainsString('"host', $result);
    }

    public function testBadgeCustomColorViaConfigure(): void
    {
        Configure::write('TailwindUi.classMapOverrides', [
            'badge.brand' => 'badge-brand',
        ]);
        Configure::write('TailwindUi.colorVariants', ['brand']);
        $this->Html = new HtmlHelper($this->View);

        $result = $this->Html->badge('Brand', ['class' => 'brand']);
        $this->assertStringContainsString('badge-brand', $result);
        // brand is promoted to a color → secondary default is suppressed
        $this->assertStringNotContainsString('badge-secondary', $result);

        $result = $this->Html->badge('Soft brand', ['class' => 'soft brand']);
        $this->assertStringContainsString('badge-soft', $result);
        $this->assertStringContainsString('badge-brand', $result);
        $this->assertStringNotContainsString('badge-secondary', $result);
    }

    public function testBadgeEscapesText(): void
    {
        $result = $this->Html->badge('<b>bold</b>');
        $this->assertStringNotContainsString('<b>', $result);
        $this->assertStringContainsString('&lt;b&gt;', $result);
    }

    public function testAlertDefault(): void
    {
        $result = $this->Html->alert('Heads up');

        $this->assertStringContainsString('class="alert alert-info"', $result);
        $this->assertStringContainsString('role="alert"', $result);
        $this->assertStringContainsString('Heads up', $result);
        $this->assertStringContainsString('<div', $result);
    }

    public function testAlertSuccessVariant(): void
    {
        $result = $this->Html->alert('Saved', ['class' => 'success']);

        $this->assertStringContainsString('alert-success', $result);
        // Default fallback (alert-info) is suppressed when a color is set.
        $this->assertStringNotContainsString('alert-info', $result);
        $this->assertStringNotContainsString('"success"', $result);
    }

    public function testAlertDangerMapsToError(): void
    {
        $result = $this->Html->alert('Boom', ['class' => 'danger']);

        $this->assertStringContainsString('alert-error', $result);
        $this->assertStringNotContainsString('alert-info', $result);
        $this->assertStringNotContainsString('alert-danger', $result);
    }

    public function testAlertErrorAliasIsRecognized(): void
    {
        $result = $this->Html->alert('Boom', ['class' => 'error']);

        $this->assertStringContainsString('alert-error', $result);
        // `error` is a recognized color, so the default fallback is suppressed.
        $this->assertStringNotContainsString('alert-info', $result);
    }

    public function testAlertWarningAndInfo(): void
    {
        $result = $this->Html->alert('Heads up', ['class' => 'warning']);
        $this->assertStringContainsString('alert-warning', $result);

        $result = $this->Html->alert('FYI', ['class' => 'info']);
        $this->assertStringContainsString('alert-info', $result);
    }

    public function testAlertEscapesText(): void
    {
        $result = $this->Html->alert('<b>boom</b>');
        $this->assertStringNotContainsString('<b>boom</b>', $result);
        $this->assertStringContainsString('&lt;b&gt;boom&lt;/b&gt;', $result);
    }

    public function testAlertEscapeFalseRendersRawHtml(): void
    {
        $result = $this->Html->alert('<strong>raw</strong>', ['escape' => false]);
        $this->assertStringContainsString('<strong>raw</strong>', $result);
    }

    public function testAlertCustomTag(): void
    {
        $result = $this->Html->alert('Note', ['tag' => 'aside']);
        $this->assertStringContainsString('<aside', $result);
        $this->assertStringNotContainsString('<div', $result);
    }

    public function testAlertExtraClassesPreserved(): void
    {
        $result = $this->Html->alert('Big', ['class' => 'success my-extra-class']);

        $this->assertStringContainsString('alert-success', $result);
        $this->assertStringContainsString('my-extra-class', $result);
    }

    public function testAlertOnKtuiPreset(): void
    {
        Configure::write('TailwindUi.classMap', 'ktui');
        $this->Html = new HtmlHelper($this->View);

        $result = $this->Html->alert('Saved', ['class' => 'success']);
        $this->assertStringContainsString('bg-green-50', $result);
        $this->assertStringContainsString('border-green-200', $result);
    }

    public function testIconDefault(): void
    {
        $result = $this->Html->icon('check');
        // Default DaisyUI uses svg tag
        $this->assertStringContainsString('<svg', $result);
        $this->assertStringContainsString('size-5', $result);
    }

    public function testIconWithKtui(): void
    {
        Configure::write('TailwindUi.classMap', 'ktui');
        $this->Html = new HtmlHelper($this->View);

        $result = $this->Html->icon('check');
        $this->assertStringContainsString('<i', $result);
        $this->assertStringContainsString('ki', $result);
        $this->assertStringContainsString('ki-filled', $result);
        $this->assertStringContainsString('check', $result);
    }
}
