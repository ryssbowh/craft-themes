<?php 

namespace Ryssbowh\CraftThemes\services;

class CacheService extends Service
{
    const CACHE_KEYS = 'themes-cache-keys';

    protected $cache;

    protected $keys = [];

    public function init()
    {
        $this->cache = \Craft::$app->cache;
        if ($keys = $this->cache->get(self::CACHE_KEYS)) {
            $this->keys = $keys;
        }
    }

    public function set(string $key, $value)
    {
        $this->cache->set($key, $value);
        if (!in_array($key, $this->keys)) {
            $this->keys[] = $key;
            $this->cacheKeys();
        }
    }

    public function get(string $key)
    {
        return $this->cache->get($key);
    }

    public function delete(string $key)
    {
        $this->cache->delete($key);
        if ($i = array_search($key, $this->keys) !== false) {
            unset($this->keys[$i]);
            $this->cacheKeys();
        }
    }

    public function flush()
    {
        foreach ($this->keys as $key) {
            $this->cache->delete($key);
        }
        $this->keys = [];
        $this->cacheKeys();
    }

    protected function cacheKeys()
    {
        $this->cache->set(self::CACHE_KEYS, $this->keys);
    }
}