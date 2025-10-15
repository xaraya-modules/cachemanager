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
 * cachemanager adminapi getcachesize function
 * @extends MethodClass<AdminApi>
 */
class GetcachesizeMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     *
     * @author jsb
     * @uses CacheInfo::getSize()
     * @param array<mixed> $args
     * @var string $type cachetype to get the size for
     * @return int size of the cache
     * @see AdminApi::getcachesize()
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
        $cacheInfo->setContext($this->getContext());
        return $cacheInfo->getSize();
    }
}
