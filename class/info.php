<?php

/**
 * Classes to provide info on the cache system of Xaraya
 *
 * @package modules\cachemanager
 * @subpackage cachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/182.html
 *
 * @author mikespub <mikespub@xaraya.com>
**/

namespace Xaraya\Modules\CacheManager;

use Xaraya\Authentication\AuthToken;
use Xaraya\Context\ContextInterface;
use Xaraya\Context\ContextTrait;
use xarMod;
use xarObject;
use xarCache;
use Throwable;
use sys;

sys::import('xaraya.context.contexttrait');
sys::import('modules.cachemanager.class.config');

class CacheInfo extends xarObject implements ContextInterface
{
    use ContextTrait;

    // list of currently supported cache types - not including 'query', 'core', 'template' here
    public static $typelist = ['page', 'block', 'module', 'object', 'variable'];
    public string $type;

    public static function init(array $args = []) {}

    /**
     * Summary of __construct
     * @param string $type cache type to get info for
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * Get the cache storage used by a particular cache type
     * @return \ixarCache_Storage|void
     */
    protected function getCacheStorage()
    {
        if (empty($this->type)) {
            return;
        }
        if ($this->type === 'token') {
            return AuthToken::getTokenStorage();
        }
        // get cache type settings
        $settings = $this->getSettings();

        // check if we have some settings for this cache type
        if (empty($settings)) {
            return;
        }

        // Get the output cache directory so you can get cache keys even if output caching is disabled
        $outputCacheDir = xarCache::getOutputCacheDir();

        // default cache storage is 'filesystem' if necessary
        if (!empty($settings['CacheStorage'])) {
            $storage = $settings['CacheStorage'];
        } else {
            $storage = 'filesystem';
        }

        // get cache storage
        $cachestorage = xarCache::getStorage([
            'storage'  => $storage,
            'type'     => $this->type,
            'cachedir' => $outputCacheDir,
        ]);

        return $cachestorage;
    }

    /**
     * Get cache type settings
     * @return array<mixed>
     */
    protected function getSettings()
    {
        return CacheConfig::getSettings($this->type);
    }

    /**
     * Construct an array of the current cache info
     *
     * @author jsb
     *
     * @return array array of cacheinfo
    */
    public function getInfo()
    {
        $cacheinfo = [];

        // get cache storage
        $cachestorage = $this->getCacheStorage();
        if (empty($cachestorage)) {
            return $cacheinfo;
        }

        // get cache info
        $cacheinfo = $cachestorage->getCacheInfo();
        $cacheinfo['total'] = $cacheinfo['hits'] + $cacheinfo['misses'];
        if (!empty($cacheinfo['total'])) {
            $cacheinfo['ratio'] = sprintf("%.1f", 100.0 * $cacheinfo['hits'] / $cacheinfo['total']);
        } else {
            $cacheinfo['ratio'] = 0.0;
        }
        if (!empty($cacheinfo['size'])) {
            $cacheinfo['size'] = round($cacheinfo['size'] / 1048576, 2);
        }
        $cacheinfo['storage'] = $cachestorage->getCacheType();

        return $cacheinfo;
    }

    /**
     * @author jsb
     *
     * @return int size of the cache
    */
    public function getSize()
    {
        $cachesize = 0;

        // get cache storage
        $cachestorage = $this->getCacheStorage();
        if (empty($cachestorage)) {
            return $cachesize;
        }

        // get cache size
        $cachesize = $cachestorage->getCacheSize();

        return $cachesize;
    }

    /**
     * Construct an array of the current cache items
     *
     * @author jsb
     *
     * @return array array of cache items
    */
    public function getList($sort = null)
    {
        $items = [];

        // get cache storage
        $cachestorage = $this->getCacheStorage();
        if (empty($cachestorage)) {
            return $items;
        }

        // get cache items
        $items = $cachestorage->getCachedList();

        // sort items
        if (empty($sort) || $sort == 'id') {
            $sort = null;
            ksort($items);
        } else {
            /** @var Module $module */
            $module = xarMod::getModule('cachemanager');
            $statsApi = $module->getStatsApi();
            $statsApi->sortitems($items, $sort);
        }

        return $items;
    }

    /**
     * Construct an array of the current cache keys
     *
     * @author jsb
     *
     * @return array sorted array of cachekeys
    */
    public function getKeys()
    {
        $cachekeys = [];

        // get cache storage
        $cachestorage = $this->getCacheStorage();
        if (empty($cachestorage)) {
            return $cachekeys;
        }

        // get cache keys
        $cachekeys = $cachestorage->getCachedKeys();

        // sort keys
        ksort($cachekeys);

        return $cachekeys;
    }

    /**
     * Construct an array of the current cache item
     *
     * @author jsb
     *
     * @param string $key the cache key
     * @param string $code the cache code (optional)
     * @return array|string array of cacheitem
    */
    public function getItem($key, $code = '')
    {
        $item = [];

        // get cache storage
        $cachestorage = $this->getCacheStorage();
        if (empty($cachestorage)) {
            return $item;
        }

        // specify suffix if necessary
        if (!empty($code)) {
            $cachestorage->setCode($code);
        }
        if ($cachestorage->isCached($key, 0, 0)) {
            $value = $cachestorage->getCached($key);
            if ($this->type == 'module' || $this->type == 'object') {
                $item = unserialize((string) $value);
            } elseif ($this->type == 'variable') {
                // check if we serialized it for storage
                if (!empty($value) && is_string($value) && strpos($value, ':serial:') === 0) {
                    try {
                        $item = unserialize(substr($value, 8));
                    } catch (Throwable $e) {
                        return $e->getMessage();
                        //$item = $value;
                    }
                } else {
                    $item = $value;
                }
            } elseif ($this->type == 'token' && !empty($value)) {
                $item = @json_decode($value, true);
            } else {
                // do nothing
                $item = $value;
            }
        }

        return $item;
    }

    /**
     * get caching configuration for a particular type
     *
     * @return CacheConfig
     */
    public function getConfig()
    {
        return CacheConfig::getCache($this->type);
    }

    /**
     * Get cache info for a particular type
     *
     * @param string $type cache type to get info for
     * @return self
     */
    public static function getCache(string $type)
    {
        return new self($type);
    }
}
