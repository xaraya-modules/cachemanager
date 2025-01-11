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
 * cachemanager adminapi regenstatic function
 * @extends MethodClass<AdminApi>
 */
class RegenstaticMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * regenerate the page output cache of URLs in the session-less list
     * @author jsb
     * @uses \CacheHooks::regenstatic()
     * @return void
     */
    public function __invoke($nolimit = null)
    {
        CacheHooks::regenstatic($nolimit);
    }
}
