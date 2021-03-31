<?php
ini_set('display_errors', 1);
function __autoload($class) {
    require_once(dirname(__FILE__) . '/../controller/' . $class . '.php');
}


$csv = $_GET['csv'] ?? false;
$uri = preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
$tmp = explode('/', $uri);

if (count($tmp) > 2) {
    $page = '404';
} else if ($tmp[1] == '') {
    $page = 'neuinfektionen';
} else {
    $page = $tmp[1];
}


if (file_exists(dirname(__FILE__). '/../controller/' . ucfirst($page) . '.php')) {

    $memcache = new Memcached;
    $memcache->addServer('localhost', 11211);
    $key = md5(json_encode(['7', $_GET, $_SERVER['REQUEST_URI']]));
    $content = $memcache->get($key);


    $cacheEnabled = false;

    if ($cacheEnabled && $content = $memcache->get($key)) {
        header('X-Cache: ' . $key);
        echo $content;
    } else {
        ob_start();
        $page = ucfirst($page);
        $c = new $page();
        if ($csv==1) {
            $c->csv();
        } else {
            $c->render();
        }

        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
        $memcache->add($key, $content, 60*60);
    }
} else {
    $c = new ErrorController();
    $c->render();
}

