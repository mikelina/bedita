<?php
/**
 * Test runner bootstrap.
 *
 * Add additional configuration/setup your application needs when running
 * unit tests in this file.
 */
require dirname(__DIR__) . '/vendor/autoload.php';

require dirname(__DIR__) . '/config/bootstrap.php';

$_SERVER['PHP_SELF'] = '/';

use BEdita\Core\Filesystem\FilesystemRegistry;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\Date;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\I18n\Time;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

TableRegistry::clear();

if (getenv('db_dsn')) {
    ConnectionManager::drop('test');
    ConnectionManager::setConfig('test', ['url' => getenv('db_dsn')]);
}
if (!defined('API_KEY')) {
    define('API_KEY', 'API_KEY');
}

Cache::drop('_bedita_object_types_');
Cache::setConfig('_bedita_object_types_', ['className' => 'Null']);

if (getenv('DEBUG_LOG_QUERIES')) {
    ConnectionManager::get('test')->logQueries(true);
    Log::setConfig('queries', [
        'className' => 'Console',
        'stream' => 'php://stdout',
        'scopes' => ['queriesLog'],
    ]);
}

$now = Time::parse('2018-01-01T00:00:00Z');
Time::setTestNow($now);
FrozenTime::setTestNow($now);
Date::setTestNow($now);
FrozenDate::setTestNow($now);

FilesystemRegistry::dropAll();
Configure::write('Filesystem', [
    'default' => [
        'className' => 'BEdita/Core.Local',
        'path' => Plugin::path('BEdita/Core') . 'tests' . DS . 'uploads',
        'baseUrl' => 'https://static.example.org/files',
    ],
    'thumbnails' => [
        'className' => 'BEdita/Core.Local',
        'path' => Plugin::path('BEdita/Core') . 'tests' . DS . 'thumbnails',
        'baseUrl' => 'https://static.example.org/thumbs',
    ],
]);
Configure::write('Thumbnails', [
    'allowAny' => false,
    'presets' => [
        'default' => [
            'generator' => 'async',
            'w' => 100,
            'h' => 100,
        ],
        'favicon-sync' => [
            'generator' => 'default',
            'w' => 16,
            'h' => 16,
            'fm' => 'png',
        ],
    ],
    'generators' => [
        'default' => [
            'className' => 'BEdita/Core.Glide',
        ],
        'async' => [
            'className' => 'BEdita/Core.Async',
        ],
    ],
]);
Configure::write('debug', true);

Cache::clear(false, '_cake_core_');
Cache::clear(false, '_cake_model_');
