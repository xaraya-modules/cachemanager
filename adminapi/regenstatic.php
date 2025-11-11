<?php

/**
 * @package modules\cachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.6.2
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\CacheManager\AdminApi;

use Xaraya\Modules\CacheManager\AdminApi;
use Xaraya\Modules\CacheManager\CacheScheduler;
use Xaraya\Modules\MethodClass;

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
     * @uses CacheScheduler::regenstatic()
     * @return void
     * @see AdminApi::regenstatic()
     */
    public function __invoke($nolimit = null): void
    {
        CacheScheduler::regenstatic($nolimit);
    }
}
