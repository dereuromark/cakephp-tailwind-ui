<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper;

use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use TailwindUi\View\Helper\BreadcrumbsHelper;

class BreadcrumbsHelperTest extends TestCase
{
    protected View $View;

    protected BreadcrumbsHelper $Breadcrumbs;

    public function setUp(): void
    {
        parent::setUp();
        Configure::delete('TailwindUi');

        Router::reload();
        $routeBuilder = Router::createRouteBuilder('/');
        $routeBuilder->connect('/{controller}', ['action' => 'index']);
        $routeBuilder->connect('/{controller}/{action}/*');

        $request = new ServerRequest([
            'webroot' => '',
            'base' => '',
            'url' => '/articles/index',
            'params' => ['controller' => 'Articles', 'action' => 'index', 'plugin' => null],
        ]);
        Router::setRequest($request);

        $this->View = new View($request);
        $this->Breadcrumbs = new BreadcrumbsHelper($this->View);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Configure::delete('TailwindUi');
        unset($this->Breadcrumbs, $this->View);
    }

    public function testWrapperClass(): void
    {
        $this->Breadcrumbs->add('Home', '/');
        $this->Breadcrumbs->add('Articles');

        $result = $this->Breadcrumbs->render();
        $this->assertStringContainsString('breadcrumbs', $result);
    }

    public function testLastCrumbIsActive(): void
    {
        $this->Breadcrumbs->add('Home', '/');
        $this->Breadcrumbs->add('Articles');

        $result = $this->Breadcrumbs->render();
        // Last crumb (Articles) has no URL → uses itemWithoutLink → gets active class
        $this->assertStringContainsString('font-semibold', $result);
        $this->assertStringContainsString('<span', $result);
        $this->assertStringContainsString('aria-current="page"', $result);
    }

    public function testNonLastCrumbHasLink(): void
    {
        $this->Breadcrumbs->add('Home', '/');
        $this->Breadcrumbs->add('Articles');

        $result = $this->Breadcrumbs->render();
        $this->assertStringContainsString('<a href="/"', $result);
    }

    public function testKtuiVariant(): void
    {
        Configure::write('TailwindUi.classMap', 'ktui');
        $this->Breadcrumbs = new BreadcrumbsHelper($this->View);

        $this->Breadcrumbs->add('Home', '/');
        $this->Breadcrumbs->add('Articles');

        $result = $this->Breadcrumbs->render();
        $this->assertStringContainsString('text-sm text-muted-foreground', $result);
        $this->assertStringContainsString('text-foreground font-medium', $result);
    }

    public function testCrumbAttributesArePreserved(): void
    {
        $this->Breadcrumbs->add('Home', '/', ['class' => 'home-item', 'data-test' => 'x']);
        $this->Breadcrumbs->add('Articles');

        $result = $this->Breadcrumbs->render();
        $this->assertStringContainsString('class="home-item"', $result);
        $this->assertStringContainsString('data-test="x"', $result);
    }

    public function testLastLinkedCrumbCanBeMarkedActive(): void
    {
        $this->Breadcrumbs->setConfig('ariaCurrent', 'lastWithLink');
        $this->Breadcrumbs->add('Home', '/');
        $this->Breadcrumbs->add('Articles', '/articles');
        $this->Breadcrumbs->add('View');

        $result = $this->Breadcrumbs->render();
        $this->assertMatchesRegularExpression('/<a href="\/articles"[^>]*aria-current="page"/', $result);
    }
}
