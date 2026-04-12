<?php
declare(strict_types=1);

namespace TailwindUi\Test\TestCase\View\Helper\FormHelper;

use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\Utility\Security;
use Cake\View\View;
use TailwindUi\View\Helper\FormHelper;

abstract class AbstractFormHelperTest extends TestCase
{
    protected View $View;
    protected FormHelper $Form;
    protected array $article;

    public function setUp(): void
    {
        parent::setUp();
        Configure::write('Config.language', 'eng');
        Configure::write('App.base', '');
        Configure::write('App.namespace', 'TailwindUi\Test\TestCase\View\Helper');
        Configure::delete('Asset');
        Configure::delete('TailwindUi');

        $request = new ServerRequest([
            'webroot' => '', 'base' => '', 'url' => '/articles/add',
            'params' => ['controller' => 'Articles', 'action' => 'add', 'plugin' => null],
        ]);
        $this->View = new View($request);
        $this->Form = new FormHelper($this->View);
        Router::reload();
        Router::setRequest($request);
        $this->article = [
            'schema' => [
                'id' => ['type' => 'integer'],
                'user_id' => ['type' => 'integer', 'null' => true],
                'title' => ['type' => 'string', 'null' => true],
                'body' => ['type' => 'text'],
                'published' => ['type' => 'boolean', 'length' => 1, 'default' => 0],
                'status' => ['type' => 'string', 'null' => true],
                '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
            ],
            'required' => ['user_id' => true, 'title' => true],
        ];
        Security::setSalt('foo!');
        $routeBuilder = Router::createRouteBuilder('/');
        $routeBuilder->connect('/{controller}', ['action' => 'index']);
        $routeBuilder->connect('/{controller}/{action}/*');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Configure::delete('TailwindUi');
        unset($this->Form, $this->View);
    }
}
