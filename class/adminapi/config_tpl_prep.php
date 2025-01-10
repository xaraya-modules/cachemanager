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
 * cachemanager adminapi config_tpl_prep function
 */
class ConfigTplPrepMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Save configuration settings in the config file and modVars
     * @author jsb <jsb@xaraya.com>
     * @access public
     * @uses \CacheManager::config_tpl_prep()
     * @param array $cachingConfiguration cachingConfiguration to be prep for a template
     * @return array of cachingConfiguration with '.' removed from keys or void
     */
    public function __invoke(array $cachingConfiguration = [])
    {
        return CacheManager::config_tpl_prep($cachingConfiguration);
    }
}
