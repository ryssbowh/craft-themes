<?php 

namespace Ryssbowh\CraftThemes\services;

/**
 * This is a helper to cache group of keys into system cache.
 * Allows to set and delete cache data by section (group) with un unknown amount of keys in each group.
 */
class CacheService extends Service
{
    const CACHE_KEYS = 'themes-cache-keys';

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var array
     */
    protected $keys = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->cache = \Craft::$app->cache;
        if ($keys = $this->cache->get(self::CACHE_KEYS)) {
            $this->keys = $keys;
        }
    }

    /**
     * set a cache group key
     * 
     * @param string $group
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $group, string $key, $value)
    {
        $this->cache->set($group.$key, $value);
        if (!in_array($key, $this->keys[$group] ?? [])) {
            $this->keys[$group][] = $key;
            $this->cacheKeys();
        }
    }

    /**
     * Get a cache value from a group and a key
     * 
     * @param  string $group
     * @param  string $key
     * @return mixed
     */
    public function get(string $group, string $key)
    {
        return $this->cache->get($group.$key);
    }

    /**
     * Delete a key in a group
     * 
     * @param  string $group
     * @param  string $key
     */
    public function delete(string $group, string $key)
    {
        $this->cache->delete($group.$key);
        if ($i = array_search($key, $this->keys[$group] ?? []) !== false) {
            unset($this->keys[$group][$i]);
            $this->cacheKeys();
        }
    }

    /**
     * Flush all keys of all groups
     */
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

    /**
     * Flush all keys in a group
     * 
     * @param  string $group
     */
    public function flushGroup(string $group)
    {
        foreach ($this->keys[$group] ?? [] as $key) {
            $this->cache->delete($group.$key);
        }
        unset($this->keys[$group]);
        $this->cacheKeys();
    }

    /**
     * Store into cache the keys defined by this service
     */
    protected function cacheKeys()
    {
        $this->cache->set(self::CACHE_KEYS, $this->keys);
    }
}