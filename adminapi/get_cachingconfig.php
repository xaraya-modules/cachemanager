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

/**
 * cachemanager adminapi get_cachingconfig function
 * @extends MethodClass<AdminApi>
 */
class GetCachingconfigMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Gets caching configuration settings in the config file or modVars
     * @author jsb <jsb@xaraya.com>
     * @access public
     * @uses CacheManager::get_config()
     * @param array<mixed> $args
     * @var string $args['from'] source of configuration to get - file or db
     * @var array $args['keys'] array of config labels and values
     * @var bool $args['tpl_prep'] prep the config for use in templates
     * @var bool $args['viahook'] config value requested as part of a hook call
     * @return array of caching configuration settings
     * @see AdminApi::getCachingconfig()
     */
    public function __invoke(array $args = [])
    {
        return CacheManager::get_config($args);
    }
}
