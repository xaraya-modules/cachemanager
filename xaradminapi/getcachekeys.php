<?php
/**
 * Construct an array of current cache keys
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.cache_manager');

/**
 * Construct an array of the current cache keys
 *
 * @author jsb
 *
 * @param $type cachetype to start the search for cachekeys
 * @return array sorted array of cachekeys
*/

function xarcachemanager_adminapi_getcachekeys($type = '')
{
    $cachekeys = [];

    // get cache type settings
    $cachetypes = xarMod::apiFunc('xarcachemanager', 'admin', 'getcachetypes');

    // check if we have some settings for this cache type
    if (empty($type) || empty($cachetypes[$type])) {
        return $cachekeys;
    }

    // Get the output cache directory so you can get cache keys even if output caching is disabled
    $outputCacheDir = xarCache::getOutputCacheDir();

    // default cache storage is 'filesystem' if necessary
    if (!empty($cachetypes[$type]['CacheStorage'])) {
        $storage = $cachetypes[$type]['CacheStorage'];
    } else {
        $storage = 'filesystem';
    }

    // get cache storage
    $cachestorage = xarCache::getStorage(['storage'  => $storage,
                                               'type'     => $type,
                                               'cachedir' => $outputCacheDir, ]);
    if (empty($cachestorage)) {
        return $cachekeys;
    }

    // get cache keys
    $cachekeys = $cachestorage->getCachedKeys();

    sort($cachekeys);

    return $cachekeys;
}
