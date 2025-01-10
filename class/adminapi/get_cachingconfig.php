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
 * cachemanager adminapi get_cachingconfig function
 */
class GetCachingconfigMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Gets caching configuration settings in the config file or modVars
     * @author jsb <jsb@xaraya.com>
     * @access public
     * @uses \CacheManager::get_config()
     * @param array $args
     * with
     *     string $args ['from'] source of configuration to get - file or db
     *     array $args ['keys'] array of config labels and values
     *     bool $args ['tpl_prep'] prep the config for use in templates
     *     bool $args ['viahook'] config value requested as part of a hook call
     * @return array of caching configuration settings
     */
    public function __invoke(array $args = [])
    {
        return CacheManager::get_config($args);
    }
}
