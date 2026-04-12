<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper;

use ArrayObject;
use Cake\Core\Configure;
use Cake\Datasource\Paging\PaginatedResultSet;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use TailwindUi\View\Helper\PaginatorHelper;

class PaginatorHelperTest extends TestCase
{
    protected View $View;

    protected PaginatorHelper $Paginator;

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
        $this->Paginator = new PaginatorHelper($this->View);

        $paginated = new PaginatedResultSet(new ArrayObject(), [
            'count' => 10,
            'totalCount' => 100,
            'perPage' => 10,
            'pageCount' => 10,
            'currentPage' => 2,
            'hasPrevPage' => true,
            'hasNextPage' => true,
            'start' => 11,
            'end' => 20,
            'alias' => 'Articles',
            'scope' => null,
            'direction' => 'asc',
            'sortDefault' => false,
            'directionDefault' => false,
            'completeSort' => [],
            'limit' => 10,
            'pages' => 10,
        ]);
        $this->Paginator->setPaginated($paginated);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Configure::delete('TailwindUi');
        unset($this->Paginator, $this->View);
    }

    public function testContainerHasJoinClass(): void
    {
        $result = $this->Paginator->links();
        $this->assertStringContainsString('class="join"', $result);
    }

    public function testItemsHaveJoinItemClass(): void
    {
        $result = $this->Paginator->links();
        $this->assertStringContainsString('join-item btn btn-sm', $result);
    }

    public function testActiveItemHasBtnActiveAndAriaCurrent(): void
    {
        $result = $this->Paginator->links();
        $this->assertStringContainsString('btn-active', $result);
        $this->assertStringContainsString('aria-current="page"', $result);
    }

    public function testKtuiConfigContainerClass(): void
    {
        Configure::write('TailwindUi.classMap', 'ktui');
        $this->Paginator = new PaginatorHelper($this->View);

        $paginated = new PaginatedResultSet(new ArrayObject(), [
            'count' => 10,
            'totalCount' => 100,
            'perPage' => 10,
            'pageCount' => 10,
            'currentPage' => 2,
            'hasPrevPage' => true,
            'hasNextPage' => true,
            'start' => 11,
            'end' => 20,
            'alias' => 'Articles',
            'scope' => null,
            'direction' => 'asc',
            'sortDefault' => false,
            'directionDefault' => false,
            'completeSort' => [],
            'limit' => 10,
            'pages' => 10,
        ]);
        $this->Paginator->setPaginated($paginated);

        $result = $this->Paginator->links();
        $this->assertStringContainsString('class="flex items-center gap-1"', $result);
        $this->assertStringContainsString('kt-btn kt-btn-sm kt-btn-outline', $result);
    }
}
