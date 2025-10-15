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
 * cachemanager adminapi getcachelist function
 * @extends MethodClass<AdminApi>
 */
class GetcachelistMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Construct an array of the current cache items
     * @author jsb
     * @uses CacheInfo::getList()
     * @param array<mixed> $args
     * @var string $type cachetype to get the cache items from
     * @return array array of cache items
     * @see AdminApi::getcachelist()
     */
    public function __invoke(array $args = ['type' => ''])
    {
        $type = '';
        $sort = null;
        if (is_array($args)) {
            extract($args);
        } else {
            $type = $args;
        }
        $cacheInfo = CacheInfo::getCache($type);
        $cacheInfo->setContext($this->getContext());
        return $cacheInfo->getList($sort);
    }
}
