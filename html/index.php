<?php
//ini_set('display_errors', 1);

function autoload($class) {
    require_once(dirname(__FILE__) . '/../controller/' . $class . '.php');
}

spl_autoload_register('autoload');

$config =include(dirname(__FILE__) . '/../config/config.php');
if ($config['settings']['dev']) {
    ini_set('display_errors', 1);
}

$csv = $_GET['csv'] ?? false;
$uri = preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
$uri = preg_replace('/\/$/', '', $_SERVER['REQUEST_URI']);
$uri = explode('?', $uri)[0];
$tmp = explode('/', $uri);


if (!isset($tmp[1]) || $tmp[1] == '') { $page = 'neuinfektionen2022daily'; } else { $page = $tmp[1]; }

if (file_exists(dirname(__FILE__). '/../controller/' . ucfirst($page) . '.php')) {

    $memcache = new Memcache;
    $memcache->addServer('127.0.0.1', 11211);
    $key = md5(json_encode(['7' . $_SERVER['SERVER_NAME'], $_GET, $_SERVER['REQUEST_URI'], json_encode($_GET)]));
    $content = $memcache->get($key);

    $cacheEnabled = ($config['settings']['caching']);

    if ($cacheEnabled && $content = $memcache->get($key)) {
        header('X-Cache: ' . $key);
        echo $content;
    } else {
        header('X-No-Cache: ' . $key);

        ob_start();
        $page = ucfirst($page);

        $controller = new $page( $tmp );
        $controller->process();

        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
        echo $memcache->add($key, $content, null, 60*60);
    }
} else {
    $c = new ErrorController($tmp);
    $c->action();
}

