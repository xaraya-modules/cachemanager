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

/**
 * cachemanager adminapi getcachetypes function
 * @extends MethodClass<AdminApi>
 */
class GetcachetypesMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     *
     * @author jsb
     * @uses CacheConfig::getTypes()
     * @return array Cache types, with key set to cache type and value set to its settings
     * @see AdminApi::getcachetypes()
     */
    public function __invoke(array $args = [])
    {
        // return the cache types and their settings
        return CacheConfig::getTypes();
    }
}
