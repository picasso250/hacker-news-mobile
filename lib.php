<?php

function _get($key = null)
{
    if ($key === null) {
        return $_GET;
    }
    return isset($_GET[$key]) ? trim($_GET[$key]) : null;
}
function _post($key = null)
{
    if ($key === null) {
        return $_POST;
    }
    return isset($_POST[$key]) ? trim($_POST[$key]) : null;
}

function get_url($url)
{
    list($code, $data) = get_url_code($url);
    if ($code != 200) {
        echo "$data\n";
        throw new Exception("status {$code}");
    }
    return $data;
}

function get_url_code_cache($url)
{
    global $services;
    $cache = $services['cache'];
    $key = 'url'.$url;
    $data = $cache->get($key);
    if ($data) {
        return $data;
    }
    list($code, $data) = $arr = get_url_code($url);
    if ($code === 200) {
        $cache->set($key, $arr, 0, time()+3600*24*7);
    }
    return $arr;
}
function get_url_code($url)
{
    $ch = curl_init($url);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt ($ch, CURLOPT_TIMEOUT, 5);
    $data = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception("curl error ".curl_error($ch), 1);
    }
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return array($code, $data);
}

function get_article($body)
{
    $uid = uniqid();
    $dom = new DOMDocument();
    $dom->loadHTML("<div id=XC$uid>$body</div>");

    $header = $dom->getElementById('header');
    $header->parentNode->removeChild($header);

    $footer = $dom->getElementById('footer');
    $footer->parentNode->removeChild($footer);

    $divs = $dom->getElementsByTagName('div');
    foreach ($divs as $div) {
        if ($div->hasAttribute('class') && $div->getAttribute('class') === 'floater') {
            $parent = $div->parentNode;
            $parent->removeChild($div);
            break;
        }
    }

    $div = $dom->getElementById("XC$uid");
    return $div->C14N();
}

function removeChild($node)
{
    $parent = $node->parentNode;
    $parent->removeChild($node);
}

function app_log($msg)
{
    $file = isset($_SERVER["HTTP_BAE_LOGID"]) ? '/home/bae/log/app.log' : 'app.log';
    $msg = call_user_func_array('sprintf', func_get_args());
}

function get_item($id, $is_force = true)
{
    global $services;
    $cache = $services['cache'];
    $key = 'item'.$id;
    $story_plain = $cache->get($key);
    if (empty($story_plain)) {
        if ($is_force) {
            $url = "https://hacker-news.firebaseio.com/v0/item/{$id}.json";
            $story_plain = get_url($url);
            app_log("get story from url %s return %s", $url, $story_plain);
            $cache->set($key, $story_plain, 0, 7200);
        } else {
            return $id;
        }
    }
    $story = json_decode($story_plain, true);
    if (json_last_error()) {
        throw new Exception("json error ".json_last_error(), 2);
    }
    return $story;
}

function render($tpl, $data = array(), $layout = null)
{
    if ($layout) {
        $data['_inner_tpl'] = $tpl;
        render($layout, $data);
    } else {
        extract($data);
        include "view/$tpl";
        ob_flush();
    }
}
