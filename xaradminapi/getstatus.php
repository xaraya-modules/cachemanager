<?php
/**
 * Utility function for admin-menu
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage CacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
/**
* utility function pass caching status to admin menu
*
* @author jsb| mikespub
* @return array containing the caching status.
*/
function cachemanager_adminapi_getstatus(array $args = [], $context = null)
{
    $status = [];

    // Security Check
    if (!xarSecurity::check('AdminXarCache')) {
        return $status;
    }

    $varCacheDir = sys::varpath() . '/cache';

    // is output caching enabled?
    if (file_exists($varCacheDir . '/output/cache.touch')) {
        $status['CachingEnabled'] = 1;
    } else {
        $status['CachingEnabled'] = 0;
    }

    // is page level output caching enbabled?
    if (file_exists($varCacheDir . '/output/cache.pagelevel')) {
        $status['PageCachingEnabled'] = 1;
        if (file_exists($varCacheDir . '/output/autocache.log')) {
            $status['AutoCachingEnabled'] = 1;
        }
    } else {
        $status['PageCachingEnabled'] = 0;
        $status['AutoCachingEnabled'] = 0;
    }

    // is block level output caching enabled?
    if (file_exists($varCacheDir . '/output/cache.blocklevel')) {
        $status['BlockCachingEnabled'] = 1;
    } else {
        $status['BlockCachingEnabled'] = 0;
    }

    // is module level output caching enabled?
    if (file_exists($varCacheDir . '/output/cache.modulelevel')) {
        $status['ModuleCachingEnabled'] = 1;
    } else {
        $status['ModuleCachingEnabled'] = 0;
    }

    // is object level output caching enabled?
    if (file_exists($varCacheDir . '/output/cache.objectlevel')) {
        $status['ObjectCachingEnabled'] = 1;
    } else {
        $status['ObjectCachingEnabled'] = 0;
    }

    // @todo this is actually in the wrong place, since it's not output caching
    // is variable level caching enabled?
    if (file_exists($varCacheDir . '/output/cache.variablelevel')) {
        $status['VariableCachingEnabled'] = 1;
    } else {
        $status['VariableCachingEnabled'] = 0;
    }

    // always active
    $status['CoreCachingEnabled'] = 1;

    // TODO: bring in line with other cache systems ?
    $status['QueryCachingEnabled'] = 0;

    return $status;
}
