<?php

/**
 * Classes to run admin gui functions
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
use xarMod;
use xarTpl;
use sys;

class CacheAdmin extends xarObject
{
    public static function init(array $args = []) {}

    /**
     * @uses cachemanager_admin_main()
     */
    public static function main()
    {
        return xarMod::guiFunc('cachemanager', 'admin', 'main');
    }

    /**
     * @uses cachemanager_admin_overview()
     */
    public static function overview()
    {
        return xarMod::guiFunc('cachemanager', 'admin', 'overview');
    }

    /**
     * @uses cachemanager_admin_modifyconfig()
     */
    public static function modifyconfig()
    {
        return xarMod::guiFunc('cachemanager', 'admin', 'modifyconfig');
    }

    /**
     * @uses cachemanager_admin_updateconfig()
     */
    public static function updateconfig()
    {
        return xarMod::guiFunc('cachemanager', 'admin', 'updateconfig');
    }

    /**
     * @uses cachemanager_admin_stats()
     */
    public static function stats(array $args = [])
    {
        return xarMod::guiFunc('cachemanager', 'admin', 'stats', $args);
    }

    /**
     * @uses cachemanager_admin_view()
     */
    public static function view(array $args = [])
    {
        return xarMod::guiFunc('cachemanager', 'admin', 'view', $args);
    }

    /**
     * @uses Config\PageCache::modifyConfig()
     */
    public static function pages(array $args = [])
    {
        sys::import('modules.cachemanager.class.config.pagecache');
        $cache = CacheConfig::getCache('page');
        $tplData = $cache->modifyConfig($args);
        if (!is_array($tplData)) {
            return $tplData;
        }
        return xarTpl::module('cachemanager', 'admin', 'pages', $tplData);
    }

    /**
     * @uses Config\BlockCache::modifyConfig()
     */
    public static function blocks(array $args = [])
    {
        sys::import('modules.cachemanager.class.config.blockcache');
        $cache = CacheConfig::getCache('block');
        $tplData = $cache->modifyConfig($args);
        if (!is_array($tplData)) {
            return $tplData;
        }
        return xarTpl::module('cachemanager', 'admin', 'blocks', $tplData);
    }

    /**
     * @uses Config\ModuleCache::modifyConfig()
     */
    public static function modules(array $args = [])
    {
        sys::import('modules.cachemanager.class.config.modulecache');
        $cache = CacheConfig::getCache('module');
        $tplData = $cache->modifyConfig($args);
        if (!is_array($tplData)) {
            return $tplData;
        }
        return xarTpl::module('cachemanager', 'admin', 'modules', $tplData);
    }

    /**
     * @uses Config\ObjectCache::modifyConfig()
     */
    public static function objects(array $args = [])
    {
        sys::import('modules.cachemanager.class.config.objectcache');
        $cache = CacheConfig::getCache('object');
        $tplData = $cache->modifyConfig($args);
        if (!is_array($tplData)) {
            return $tplData;
        }
        return xarTpl::module('cachemanager', 'admin', 'objects', $tplData);
    }

    /**
     * @uses Config\VariableCache::modifyConfig()
     */
    public static function variables(array $args = [])
    {
        sys::import('modules.cachemanager.class.config.variablecache');
        $cache = CacheConfig::getCache('variable');
        $tplData = $cache->modifyConfig($args);
        if (!is_array($tplData)) {
            return $tplData;
        }
        return xarTpl::module('cachemanager', 'admin', 'variables', $tplData);
    }

    /**
     * @uses Config\QueryCache::modifyConfig()
     */
    public static function queries(array $args = [])
    {
        sys::import('modules.cachemanager.class.config.querycache');
        $cache = CacheConfig::getCache('query');
        $tplData = $cache->modifyConfig($args);
        if (!is_array($tplData)) {
            return $tplData;
        }
        return xarTpl::module('cachemanager', 'admin', 'queries', $tplData);
    }

    /**
     * @uses Config\TemplateCache::modifyConfig()
     */
    public static function templates(array $args = [])
    {
        sys::import('modules.cachemanager.class.config.templatecache');
        $cache = CacheConfig::getCache('template');
        $tplData = $cache->modifyConfig($args);
        if (!is_array($tplData)) {
            return $tplData;
        }
        return xarTpl::module('cachemanager', 'admin', 'templates', $tplData);
    }

    /**
     * @uses cachemanager_admin_flushcache()
     */
    public static function flushcache(array $args = [])
    {
        return xarMod::guiFunc('cachemanager', 'admin', 'flushcache', $args);
    }
}
