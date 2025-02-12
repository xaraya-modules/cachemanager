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
 * cachemanager adminapi getobjects function
 * @extends MethodClass<AdminApi>
 */
class GetobjectsMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * get configuration of object caching for all objects
     * @uses ObjectCache::getConfig()
     * @return array object caching configurations
     * @see AdminApi::getobjects()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        // Get all object cache settings
        $cacheConfig = CacheConfig::getCache('object');
        $cacheConfig->setContext($this->getContext());
        return $cacheConfig->getConfig();
    }
}
