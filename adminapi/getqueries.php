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
 * cachemanager adminapi getqueries function
 * @extends MethodClass<AdminApi>
 */
class GetqueriesMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * get configuration of query caching for expensive queries
     * @uses QueryCache::getConfig()
     * @return array of query caching configurations
     * @see AdminApi::getqueries()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        // Get all query cache settings
        $cacheConfig = CacheConfig::getCache('query');
        $cacheConfig->setContext($this->getContext());
        return $cacheConfig->getConfig();
    }
}
