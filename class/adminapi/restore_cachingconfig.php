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
use Xaraya\Modules\CacheManager\CacheManager;
use Xaraya\Modules\MethodClass;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * cachemanager adminapi restore_cachingconfig function
 * @extends MethodClass<AdminApi>
 */
class RestoreCachingconfigMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Restore the caching configuration file
     * @author jsb <jsb@xaraya.com>
     * @access public
     * @uses \CacheManager::restore_config()
     * @return bool
     */
    public function __invoke(array $args = [])
    {
        return CacheManager::restore_config();
    }
}
