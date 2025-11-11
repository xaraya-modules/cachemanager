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

use Xaraya\Modules\AdminApiClass;

/**
 * Handle the cachemanager admin API
 *
 * @method mixed configTplPrep(array $args)
 * @method mixed convertseconds(array $args)
 * @method mixed createhook(array $args)
 * @method mixed deletehook(array $args)
 * @method mixed getCachingconfig(array $args)
 * @method mixed getblocks(array $args)
 * @method mixed getcachedirs(mixed $dir = false)
 * @method mixed getcacheinfo(array $args)
 * @method mixed getcacheitem(array $args)
 * @method mixed getcachekeys(array $args)
 * @method mixed getcachelist(array $args)
 * @method mixed getcachesize(array $args)
 * @method mixed getcachetypes(array $args = [])
 * @method mixed getmenulinks(array $args)
 * @method mixed getmodules(array $args)
 * @method mixed getobjects(array $args)
 * @method mixed getqueries(array $args)
 * @method mixed getstatus(array $args = [])
 * @method mixed getstoragetypes(array $args = [])
 * @method mixed getvariables(array $args)
 * @method mixed regenstatic(array $args)
 * @method mixed restoreCachingconfig(array $args)
 * @method mixed saveCachingconfig(array $args)
 * @method mixed updateconfighook(array $args)
 * @method mixed updatehook(array $args)
 * @extends AdminApiClass<Module>
 */
class AdminApi extends AdminApiClass
{
    // ...
}
