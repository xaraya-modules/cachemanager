<?php

/**
 * Classes to manage config for the cache system of Xaraya
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

use xarObject;
use sys;

sys::import('modules.cachemanager.class.manager');

class CacheConfig extends xarObject
{
    // list of currently supported cache types - not including 'query', 'core', 'template' here
    public static $typelist = ['page', 'block', 'module', 'object', 'variable'];
    public static $cachetypes = [];
    public string $type;

    public static function init(array $args = []) {}

    /**
     * @author jsb
     *
     * @return array Cache types, with key set to cache type and value set to its settings
     */
    public static function getTypes()
    {
        if (!empty(static::$cachetypes)) {
            return static::$cachetypes;
        }

        // get the caching config settings from the config file
        $settings = CacheManager::getConfigFromFile();

        // map the settings to the right cache type
        $cachetypes = [];
        foreach (static::$typelist as $type) {
            $cachetypes[$type] = [];
            foreach (array_keys($settings) as $setting) {
                if (preg_match("/^$type\.(.+)$/i", $setting, $matches)) {
                    $info = $matches[1];
                    $cachetypes[$type][$info] = $settings[$setting];
                }
            }
            // default cache storage is 'filesystem' if necessary
            if (empty($cachetypes[$type]['CacheStorage'])) {
                $cachetypes[$type]['CacheStorage'] = 'filesystem';
            }
        }
        static::$cachetypes = $cachetypes;

        // return the cache types and their settings
        return static::$cachetypes;
    }

    /**
     * Summary of getSettings
     * @param string $type
     * @return array<mixed>
     */
    public static function getSettings(string $type)
    {
        return static::getTypes()[$type] ?? [];
    }

    /**
     * Summary of __construct
     * @param string $type cache type to get config for
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * configure caching for any type - overridden
     * @return array|void
     */
    public function modifyConfig($args)
    {
        // ...
    }

    /**
     * get configuration of caching for any type - overridden
     *
     * @return array caching configurations
     */
    public function getConfig()
    {
        return [];
    }

    /**
     * Get cache info for a particular type
     *
     * @return CacheInfo
     */
    public function getInfo()
    {
        return CacheInfo::getCache($this->type);
    }

    /**
     * get caching configuration for a particular type
     *
     * @param string $type cache type
     * @return self
     */
    public static function getCache(string $type)
    {
        $typeclass = [
            'block' => Config\BlockCache::class,
            'module' => Config\ModuleCache::class,
            'object' => Config\ObjectCache::class,
            'page' => Config\PageCache::class,
            'query' => Config\QueryCache::class,
            'template' => Config\TemplateCache::class,
            'variable' => Config\VariableCache::class,
        ];
        return new $typeclass[$type]($type);
    }
}
