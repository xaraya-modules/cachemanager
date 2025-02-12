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
sys::import('modules.cachemanager.adminapi');

/**
 * Handle the cachemanager admin GUI
 *
 * @method mixed blocks(array $args)
 * @method mixed flushcache(array $args)
 * @method mixed main(array $args)
 * @method mixed modifyconfig(array $args)
 * @method mixed modifyhook(array $args)
 * @method mixed modules(array $args)
 * @method mixed objects(array $args)
 * @method mixed overview(array $args)
 * @method mixed pages(array $args)
 * @method mixed queries(array $args)
 * @method mixed stats(array $args)
 * @method mixed updateconfig(array $args)
 * @method mixed variables(array $args)
 * @method mixed view(array $args)
 * @extends AdminGuiClass<Module>
 */
class AdminGui extends AdminGuiClass
{
    public function getStatsAPI(): StatsApi
    {
        return $this->getModule()->getStatsAPI();
    }
}
