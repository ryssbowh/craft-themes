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

    public function set(string $group, string $key, $value)
    {
        $this->cache->set($group.$key, $value);
        if (!in_array($key, $this->keys[$group] ?? [])) {
            $this->keys[$group][] = $key;
            $this->cacheKeys();
        }
    }

    public function get(string $group, string $key)
    {
        return $this->cache->get($group.$key);
    }

    public function delete(string $group, string $key)
    {
        $this->cache->delete($group.$key);
        if ($i = array_search($key, $this->keys[$group] ?? []) !== false) {
            unset($this->keys[$group][$i]);
            $this->cacheKeys();
        }
    }

    public function flush()
    {
        foreach ($this->keys as $group => $keys) {
            foreach ($keys as $key) {
                $this->cache->delete($group.$key);
            }
        }
        $this->keys = [];
        $this->cacheKeys();
    }

    public function flushGroup(string $group)
    {
        foreach ($this->keys[$group] ?? [] as $key) {
            $this->cache->delete($group.$key);
        }
        unset($this->keys[$group]);
        $this->cacheKeys();
    }

    protected function cacheKeys()
    {
        $this->cache->set(self::CACHE_KEYS, $this->keys);
    }
}