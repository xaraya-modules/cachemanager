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
use Xaraya\Modules\CacheManager\CacheInfo;
use Xaraya\Modules\MethodClass;
use sys;

sys::import('xaraya.modules.method');

/**
 * cachemanager adminapi getcacheitem function
 * @extends MethodClass<AdminApi>
 */
class GetcacheitemMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Construct an array of the current cache item
     * @author jsb
     * @uses CacheInfo::getItem()
     * @param array<mixed> $args
     * @var string $type cachetype to get the cache item from, with $args['key'] the cache key
     * @return array array of cacheitem
     * @see AdminApi::getcacheitem()
     */
    public function __invoke($args = ['type' => '', 'key' => '', 'code' => ''])
    {
        $type = '';
        $key = '';
        $code = '';
        if (is_array($args)) {
            extract($args);
        } else {
            $type = $args;
        }
        $cacheInfo = CacheInfo::getCache($type);
        $cacheInfo->setContext($this->getContext());
        return $cacheInfo->getItem($key, $code);
    }
}
