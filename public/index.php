<?PHP
use Phalcon\Config\Adapter\Ini as ConfigIni;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// 读取配置
$config = new ConfigIni(
    APP_PATH . "/config/config.ini"
);

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
        APP_PATH.'/utils/',
    ]
);

$loader->registerNamespaces(
    [
        'logic'    => APP_PATH . '/logic/',
        'Utils'    => APP_PATH.'/utils/',

    ]
);

$loader->register();

// Create a DI
$di = new FactoryDefault();

// Setup the view component
$di->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$di->setShared('view', function () {
    $view = new View();
    $view->setDI($this);
    $view->setViewsDir(APP_PATH . '/views/');

    $view->registerEngines([
        '.volt' => function ($view) {

            $volt = new VoltEngine($view, $this);

            $volt->setOptions([
                'compiledPath' => BASE_PATH . '/cache/',
                'compiledSeparator' => '_'
            ]);

            return $volt;
        },
        '.phtml' => PhpEngine::class

    ]);

    return $view;
});

// Setup a base URI so that all generated URIs include the "tutorial" folder
$di->set(
    'url',
    function () {
        $url = new UrlProvider();
        $url->setBaseUri('/');
        return $url;
    }
);


$database = $config->database;

//设置数据库
$di->set(
    "db",
    function () use($database){
        return new DbAdapter(
            [
                "host"     => $database['host'],
                "username" => $database['username'],
                "password" => $database['password'],
                "dbname"   => $database['dbname'],
            ]
        );
    }
);



$di->set('curlHelper', function(){
    return new CurlHelper();
});

$di->set('curlMulti', function(){
    return new curlMulti_core();
});

$di->set('dateHelper', function(){
    return new DateHelper();
});

$di->set('fileHelper', function(){
    return new FileHelper();
});

$application = new Application($di);

//common functions
include APP_PATH . '/common.php';

try {
    // Handle the request
    $response = $application->handle();
 
    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}




