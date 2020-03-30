<?php

require 'lib.php';
$env = parse_ini_file(__DIR__ . '/.env', true);
$services = require __DIR__ . '/services.php';

header('Cache-Control:private, max-age=0');
header('Content-Type: text/html; charset=UTF-8');

$cache = $services['cache'];
$key = 'topstories_key';
$topstories_plain = $cache->get($key);
if (empty($topstories_plain)) {
    $url = 'https://hacker-news.firebaseio.com/v0/topstories.json';
    $topstories_plain = get_url($url);
    app_log("get topstories from url %s", $url);
    $cache->set($key, $topstories_plain, 0, 600);
}
$topstories = json_decode($topstories_plain, true);
if (json_last_error()) {
    throw new Exception("json error " . json_last_error(), 1);
}

render('index.html', compact('topstories'), 'master.html');