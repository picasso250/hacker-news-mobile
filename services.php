<?php

$envm = isset($env['memcached']) ? $env['memcached'] : [];
$mc = new Memcached();
$mc->addServers(array(
    array(isset($envm['host']) ? $envm['host'] : 'localhost', isset($envm['port']) ? $envm['port'] : 11211),
));
return array(
    'cache' => $mc,
);
