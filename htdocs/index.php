<?php
//ini_set('display_errors', 1);

function __autoload($class) {
    require_once(dirname(__FILE__) . '/../controller/' . $class . '.php');
}

$config = json_decode(file_get_contents(dirname(__FILE__) . '/../config/config.json'), 1);
if ($config['settings']['dev']) {
    ini_set('display_errors', 1);
}

$csv = $_GET['csv'] ?? false;
$uri = preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
$uri = preg_replace('/\/$/', '', $_SERVER['REQUEST_URI']);
$uri = explode('?', $uri)[0];
$tmp = explode('/', $uri);


if (!isset($tmp[1]) || $tmp[1] == '') { $page = 'altersgruppen'; } else { $page = $tmp[1]; }

if (file_exists(dirname(__FILE__). '/../controller/' . ucfirst($page) . '.php')) {

    $memcache = new Memcached;
    $memcache->addServer('localhost', 11211);
    $key = md5(json_encode(['7' . $_SERVER['SERVER_NAME'], $_GET, $_SERVER['REQUEST_URI']]));
    $content = $memcache->get($key);


    $cacheEnabled = ($config['settings']['caching']) && $page != 'kontakt';

    if ($cacheEnabled && $content = $memcache->get($key)) {
        header('X-Cache: ' . $key);
        echo $content;
    } else {
        ob_start();
        $page = ucfirst($page);

        $controller = new $page( $tmp );
        $controller->process();

        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
        $memcache->add($key, $content, 60*60);
    }
} else {
    $c = new ErrorController($tmp);
    $c->action();
}

