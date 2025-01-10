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

use Xaraya\Modules\CacheManager\CacheHooks;
use Xaraya\Modules\MethodClass;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * cachemanager admin modifyhook function
 */
class ModifyhookMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * modify an entry for a module item - hook for ('item','modify','GUI')
     * @uses \CacheHooks::modifyhook()
     * @param array $args with mandatory arguments:
     * - int   $args['objectid'] ID of the object
     * - array $args['extrainfo'] extra information
     * @return string hook output in HTML
     */
    public function __invoke(array $args = [])
    {
        return CacheHooks::modifyhook($args, $this->getContext());
    }
}
