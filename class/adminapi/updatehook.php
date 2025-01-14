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
use Xaraya\Modules\CacheManager\CacheHooks;
use Xaraya\Modules\MethodClass;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * cachemanager adminapi updatehook function
 * @extends MethodClass<AdminApi>
 */
class UpdatehookMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * update entry for a module item - hook for ('item','update','API')
     * Optional $extrainfo['cachemanager_remark'] from arguments, or 'cachemanager_remark' from input
     * @uses CacheHooks::updatehook()
     * @param array<mixed> $args with mandatory arguments:
     * - int   $args['objectid'] ID of the object
     * - array $args['extrainfo'] extra information
     * @return array updated extrainfo array
     */
    public function __invoke(array $args = [])
    {
        return CacheHooks::updatehook($args, $this->getContext());
    }
}
