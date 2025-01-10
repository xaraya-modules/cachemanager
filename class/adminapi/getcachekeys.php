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

use Xaraya\Modules\CacheManager\CacheInfo;
use Xaraya\Modules\MethodClass;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * cachemanager adminapi getcachekeys function
 */
class GetcachekeysMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Construct an array of the current cache keys
     * @author jsb
     * @uses \CacheInfo::getKeys()
     * @param array $args ['type'] cachetype to get the cache keys from
     * @return array sorted array of cachekeys
     */
    public function __invoke(array $args = ['type' => ''])
    {
        $type = '';
        if (is_array($args)) {
            extract($args);
        } else {
            $type = $args;
        }
        $cacheInfo = CacheInfo::getCache($type);
        return $cacheInfo->getKeys();
    }
}
