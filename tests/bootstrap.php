<?php
declare(strict_types=1);

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use TailwindUi\TailwindUiPlugin;

if (is_file('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
} else {
    require_once dirname(__DIR__) . '/vendor/autoload.php';
}

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
define('ROOT', dirname(__DIR__));
define('APP_DIR', 'TestApp');
define('WEBROOT_DIR', 'webroot');
define('TMP', sys_get_temp_dir() . DS);
define('LOGS', TMP . 'logs' . DS);
define('CACHE', TMP . 'cache' . DS);
define('SESSIONS', TMP . 'sessions' . DS);
define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'vendor' . DS . 'cakephp' . DS . 'cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('TEST_APP', ROOT . DS . 'tests' . DS . 'test_app' . DS);
define('APP', TEST_APP . 'TestApp' . DS);
define('WWW_ROOT', TEST_APP . 'webroot' . DS);
define('CONFIG', TEST_APP . 'config' . DS);

@mkdir(LOGS);
@mkdir(SESSIONS);
@mkdir(CACHE);
@mkdir(CACHE . 'views');
@mkdir(CACHE . 'models');

require_once CORE_PATH . 'config/bootstrap.php';

date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');

Configure::write('debug', true);
Configure::write('App', [
    'namespace' => 'TestApp',
    'encoding' => 'UTF-8',
    'base' => false,
    'baseUrl' => false,
    'fullBaseUrl' => 'http://localhost',
    'paths' => [
        'plugins' => [dirname(APP) . DS . 'plugins' . DS],
        'templates' => [TEST_APP . 'templates' . DS],
    ],
]);

Cache::setConfig([
    '_cake_translations_' => ['engine' => 'File', 'prefix' => 'cake_translations_', 'serialize' => true],
    '_cake_core_' => ['engine' => 'File', 'prefix' => 'cake_core_', 'serialize' => true],
    '_cake_model_' => ['engine' => 'File', 'prefix' => 'cake_model_', 'serialize' => true],
]);

Plugin::getCollection()->add(new TailwindUiPlugin(['path' => ROOT . DS]));
