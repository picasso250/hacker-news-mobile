<?php

require 'lib.php';
$env = parse_ini_file(__DIR__ . '/.env', true);
$services = require __DIR__ . '/services.php';

header('Cache-Control:private, max-age=0');
header('Content-Type: text/html; charset=UTF-8');

$REQUEST_URI = $_SERVER['REQUEST_URI'];
$arr = explode('?', $REQUEST_URI);
$url = $arr[0];
if (preg_match('%^/story/(\d+)%', $url, $matches)) {
    $id = $matches[1];
    $story = get_item($id);
    render('story-page.html', compact('story'), 'master.html');
    exit;
}
if (preg_match('%^/get-(story|comment)/(\d+)%', $url, $matches)) {
    $type = $matches[1];
    $id = $matches[2];
    $$type = get_item($id);
    render("{$type}-item-inner.html", compact($type));
    exit;
}
if (preg_match('%^/get-url$%', $url, $matches)) {
    $url = _post('url');
    if (empty($url)) {
        exit;
    }
    list($code, $html) = get_url_code_cache($url);
    if ($code !== 200) {
        echo "fetch $code";
        exit;
    }
    if (!preg_match('%<body[ >]*?>([\s\S]+)</body>%m', $html, $matches)) {
        echo 'no body';
        exit;
    }
    $body = $matches[1];
    echo get_article($body);
    exit;
}

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
