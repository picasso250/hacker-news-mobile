<?php

class Memcache_SAE
{
    private $mem;
    public function __construct()
    {
        $this->mem = memcache_init();
    }
    public function get($key)
    {
        return memcache_get($this->mem, $key);
    }
    public function set($key, $value, $f, $time)
    {
        return memcache_set($this->mem, $key, $value, $f, $time);
    }
    public function addServer()
    {}
}
