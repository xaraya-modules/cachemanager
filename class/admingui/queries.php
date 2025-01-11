<?php

/**
 * @package modules\cachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\CacheManager\AdminGui;


use Xaraya\Modules\CacheManager\AdminGui;
use Xaraya\Modules\CacheManager\CacheConfig;
use Xaraya\Modules\MethodClass;
use sys;

sys::import('xaraya.modules.method');

/**
 * cachemanager admin queries function
 * @extends MethodClass<AdminGui>
 */
class QueriesMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * configure query caching (TODO)
     * @uses \QueryCache::modifyConfig()
     * @return array
     */
    public function __invoke(array $args = [])
    {
        $cache = CacheConfig::getCache('query');
        return $cache->modifyConfig($args);
    }
}
