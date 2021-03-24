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
        $this->keys[$key] = $value;
        $this->cacheKeys($key);
    }

    public function get(string $key)
    {
        return $this->cache->get($key);
    }

    public function delete(string $key)
    {
        if (isset($this->keys[$key])) {
            unset($this->keys[$key]);
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
        $this->cache->set(self::CACHE_KEYS, array_keys($this->keys));
    }
}