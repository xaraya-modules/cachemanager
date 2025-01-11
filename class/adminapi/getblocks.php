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
use Xaraya\Modules\CacheManager\CacheConfig;
use Xaraya\Modules\MethodClass;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * cachemanager adminapi getblocks function
 * @extends MethodClass<AdminApi>
 */
class GetblocksMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * get configuration of block caching for all blocks
     * @uses \BlockCache::getConfig()
     * @return array Block caching configurations
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        // Get all block cache settings
        $cache = CacheConfig::getCache('block');
        return $cache->getConfig();
    }
}
