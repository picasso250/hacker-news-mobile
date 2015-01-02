<?php

if (isset($_SERVER["HTTP_APPNAME"])) {
    include 'Memcache_SAE.php';
    return array(
        'cache' => new Memcache_SAE(),
    );
} else {
    include 'Memcache.php';
    $memcache_obj = new Memcache;
    $memcache_obj->addServer('nmg01-ssl-gxt-test-meta.nmg01.baidu.com', 8201);
    return array(
        'cache' => $memcache_obj,
        // 'db' => new Pdo('mysql:host=localhost;port=3306;dbname=hackernews', 'root', ''),
    );
}
