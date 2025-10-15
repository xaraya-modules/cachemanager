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

sys::import('xaraya.modules.method');

/**
 * cachemanager adminapi getmodules function
 * @extends MethodClass<AdminApi>
 */
class GetmodulesMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * get configuration of module caching for all modules
     * @uses ModuleCache::getConfig()
     * @return array module caching configurations
     * @see AdminApi::getmodules()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        // Get all module cache settings
        $cacheConfig = CacheConfig::getCache('module');
        $cacheConfig->setContext($this->getContext());
        return $cacheConfig->getConfig();
    }
}
