<?php

/**
 * @package modules\cachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\CacheManager\AdminApi;


use Xaraya\Modules\CacheManager\AdminApi;
use Xaraya\Modules\MethodClass;
use xarSecurity;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * cachemanager adminapi getstatus function
 * @extends MethodClass<AdminApi>
 */
class GetstatusMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * utility function pass caching status to admin menu
     * @author jsb| mikespub
     * @return array containing the caching status.
     */
    public function __invoke(array $args = [])
    {
        $status = [];

        // Security Check
        if (!$this->sec()->checkAccess('AdminXarCache')) {
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
            } else {
                $status['AutoCachingEnabled'] = 0;
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
}
