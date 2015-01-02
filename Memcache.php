<?php

class Memcache
{
    private function getFileName($key)
    {
        return __DIR__.'/cache/'.urlencode($key);
    }
    public function get($key)
    {
        $f = $this->getFileName($key);
        if (is_file($f)) {
            return unserialize(file_get_contents($f));
        }
        return null;
    }
    public function set($key, $value)
    {
        return file_put_contents($this->getFileName($key), serialize($value));
    }
    public function addServer()
    {}
}
