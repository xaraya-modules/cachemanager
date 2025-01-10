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

use Xaraya\Modules\CacheManager\CacheManager;
use Xaraya\Modules\MethodClass;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * cachemanager adminapi save_cachingconfig function
 */
class SaveCachingconfigMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Save configuration settings in the config file and modVars
     * @author jsb <jsb@xaraya.com>
     * @access public
     * @uses \CacheManager::save_config()
     * @param array $args
     * with
     *     mixed $args ['config'] array of config labels and values
     */
    public function __invoke(array $args = [])
    {
        return CacheManager::save_config($args);
    }
}
