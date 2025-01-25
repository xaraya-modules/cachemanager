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
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * cachemanager adminapi getcacheinfo function
 * @extends MethodClass<AdminApi>
 */
class GetcacheinfoMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Construct an array of the current cache info
     * @author jsb
     * @uses CacheInfo::getInfo()
     * @param array<mixed> $args
     * @var string $type cachetype to start the search for cacheinfo
     * @return array array of cacheinfo
     * @see AdminApi::getcacheinfo()
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
        return $cacheInfo->getInfo();
    }
}
