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
 * cachemanager admin blocks function
 * @extends MethodClass<AdminGui>
 */
class BlocksMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * configure block caching
     * @uses BlockCache::modifyConfig()
     * @return array
     * @see AdminGui::blocks()
     */
    public function __invoke(array $args = [])
    {
        $cacheConfig = CacheConfig::getCache('block');
        $cacheConfig->setContext($this->getContext());
        return $cacheConfig->modifyConfig($args);
    }
}
