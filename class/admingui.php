<?php

/**
 * @package modules\cachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
 **/

namespace Xaraya\Modules\CacheManager;

use Xaraya\Modules\AdminGuiClass;
use sys;

sys::import('xaraya.modules.admingui');
sys::import('modules.cachemanager.class.adminapi');

/**
 * Handle the cachemanager admin GUI
 * @extends AdminGuiClass<Module>
 */
class AdminGui extends AdminGuiClass
{
    public function getStatsAPI(): StatsApi
    {
        return $this->getModule()->getStatsAPI();
    }
}
