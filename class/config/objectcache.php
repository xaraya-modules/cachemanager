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

namespace Xaraya\Modules\CacheManager\Config;

use xarCache;
use xarOutputCache;
use xarPageCache;
use xarMod;
use xarObjectCache;
use sys;

sys::import('modules.cachemanager.class.config');
sys::import('modules.cachemanager.class.utility');
use Xaraya\Modules\CacheManager\CacheConfig;
use Xaraya\Modules\CacheManager\CacheUtility;

class ObjectCache extends CacheConfig
{
    public static function init(array $args = []) {}

    /**
     * configure object caching
     * @return array|void
     */
    public function modifyConfig($args)
    {
        extract($args);

        if (!$this->sec()->checkAccess('AdminXarCache')) {
            return;
        }

        $data = [];
        if (!xarCache::isOutputCacheEnabled() || !xarOutputCache::isObjectCacheEnabled()) {
            $data['objects'] = [];
            return $data;
        }

        $this->var()->get('submit', $submit, 'str', '');
        if (!empty($submit)) {
            // Confirm authorisation code
            if (!$this->sec()->confirmAuthKey()) {
                return;
            }

            $this->var()->get('docache', $docache, 'isset', []);
            $this->var()->get('usershared', $usershared, 'isset', []);
            $this->var()->get('cacheexpire', $cacheexpire, 'isset', []);

            $newobjects = [];
            // loop over something that should return values for every object
            foreach ($cacheexpire as $name => $expirelist) {
                $newobjects[$name] = [];
                foreach ($expirelist as $method => $expire) {
                    $newobjects[$name][$method] = [];
                    // flip from docache in template to nocache in settings
                    if (!empty($docache[$name]) && !empty($docache[$name][$method])) {
                        $newobjects[$name][$method]['nocache'] = 0;
                    } else {
                        $newobjects[$name][$method]['nocache'] = 1;
                    }
                    if (!empty($usershared[$name]) && !empty($usershared[$name][$method])) {
                        $newobjects[$name][$method]['usershared'] = intval($usershared[$name][$method]);
                    } else {
                        $newobjects[$name][$method]['usershared'] = 0;
                    }
                    if (!empty($expire)) {
                        $expire = CacheUtility::convertToSeconds($expire);
                    } elseif ($expire === '0') {
                        $expire = 0;
                    } else {
                        $expire = null;
                    }
                    $newobjects[$name][$method]['cacheexpire'] = $expire;
                }
            }
            // save settings to dynamicdata in case cachemanager is removed later
            $this->mod('dynamicdata')->setVar('objectcache_settings', serialize($newobjects));

            // objects could be anywhere, we're not smart enough not know exactly where yet
            $key = '';
            // so just flush all pages
            if (xarOutputCache::isPageCacheEnabled()) {
                xarPageCache::flushCached($key);
            }
            // and flush the objects
            xarObjectCache::flushCached($key);
            if ($this->mod()->getVar('AutoRegenSessionless')) {
                xarMod::apiFunc('cachemanager', 'admin', 'regenstatic');
            }
        }

        // Get all object caching configurations
        $data['objects'] = $this->getConfig();

        $data['authid'] = $this->sec()->genAuthKey();
        return $data;
    }

    /**
     * get configuration of object caching for all objects
     *
     * @return array object caching configurations
     */
    public function getConfig()
    {
        // Get all object cache settings
        $objectsettings = [];
        $serialsettings = $this->mod('dynamicdata')->getVar('objectcache_settings');
        if (!empty($serialsettings)) {
            $objectsettings = unserialize($serialsettings);
        }

        // Get all objects
        $objects = xarMod::apiFunc('dynamicdata', 'user', 'getobjects');

        // Get default object methods to cache
        $defaultobjectmethods = unserialize((string) $this->mod()->getVar('DefaultObjectCacheMethods'));

        // CHECKME: do we want to support settings for non-objects (like tables) ?

        $objectconfig = [];
        foreach (array_keys($objects) as $id) {
            // TODO: filter on visibility, dummy datastores etc. ?
            if ($objects[$id]['objectid'] < 4 ||
                $objects[$id]['moduleid'] == xarMod::getRegID('roles') ||
                $objects[$id]['moduleid'] == xarMod::getRegID('privileges')) {
                continue;
            }
            // use the object name as key for easy lookup in xarObjectCache
            $name = $objects[$id]['name'];
            $objectconfig[$name] = $objects[$id];
            $objectconfig[$name]['cachesettings'] = [];
            if (isset($objectsettings[$name])) {
                foreach ($objectsettings[$name] as $method => $settings) {
                    if ($settings['cacheexpire'] > 0) {
                        $settings['cacheexpire'] = CacheUtility::convertFromSeconds($settings['cacheexpire']);
                    }
                    $objectconfig[$name]['cachesettings'][$method] = $settings;
                }
            }
            // TODO: Try loading some defaults from the object config ?
            foreach ($defaultobjectmethods as $method => $docache) {
                if (isset($objectconfig[$name]['cachesettings'][$method])) {
                    continue;
                }
                $settings = [];
                // flip from docache in config to nocache in settings
                if (!empty($docache)) {
                    $settings['nocache'] = 0;
                } else {
                    $settings['nocache'] = 1;
                }
                $settings['usershared'] = 1;
                $settings['cacheexpire'] = '';
                $objectconfig[$name]['cachesettings'][$method] = $settings;
            }
        }
        return $objectconfig;
    }
}
