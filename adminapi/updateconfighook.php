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
use xarCache;
use xarOutputCache;
use xarPageCache;
use xarBlockCache;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * cachemanager adminapi updateconfighook function
 * @extends MethodClass<AdminApi>
 */
class UpdateconfighookMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * update entry for a module item - hook for ('item','updateconfig','API')
     * Optional $extrainfo['cachemanager_remark'] from arguments, or 'cachemanager_remark' from input
     * @param array<mixed> $args with mandatory arguments:
     * - array $args['extrainfo'] extra information
     * @return array updated extrainfo array
     * @see AdminApi::updateconfighook()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (!isset($extrainfo) || !is_array($extrainfo)) {
            $extrainfo = [];
        }

        if (!xarCache::isOutputCacheEnabled()) {
            // nothing more to do here
            return $extrainfo;
        }

        // When called via hooks, modname wil be empty, but we get it from the
        // extrainfo or the current module
        if (empty($modname)) {
            if (!empty($extrainfo['module'])) {
                $modname = $extrainfo['module'];
            } else {
                $modname = $this->mod()->getName();
            }
        }
        $modid = $this->mod()->getRegID($modname);
        if (empty($modid)) {
            $msg = $this->ml(
                'Invalid #(1) for #(2) function #(3)() in module #(4)',
                'module name',
                'admin',
                'updatehook',
                'cachemanager'
            );
            throw new BadParameterException(null, $msg);
        }

        if (!isset($itemtype) || !is_numeric($itemtype)) {
            if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
                $itemtype = $extrainfo['itemtype'];
            } else {
                $itemtype = 0;
            }
        }

        // TODO: make all the module cache flushing behavior admin configurable

        switch ($modname) {
            case 'base': // who knows what global impact a config change to base might make
                // flush everything.
                if (xarOutputCache::isPageCacheEnabled()) {
                    xarPageCache::flushCached('');
                }
                if (xarOutputCache::isBlockCacheEnabled()) {
                    xarBlockCache::flushCached('');
                }
                break;
            case 'autolinks': // fall-through all hooked utility modules that are admin config modified
            case 'comments': // keep falling through
            case 'keywords': // keep falling through
                // delete cachekey of each module autolinks is hooked to.
                if (xarOutputCache::isPageCacheEnabled()) {
                    $hooklist = $this->mod()->apiFunc('modules', 'admin', 'gethooklist');
                    $modhooks = reset($hooklist[$modname]);

                    foreach ($modhooks as $hookedmodname => $hookedmod) {
                        $cacheKey = "$hookedmodname-";
                        xarPageCache::flushCached($cacheKey);
                    }
                }
                // incase it's got a user view, like categories.
                // no break
            case 'articles': // fall-through
                //nothing special yet
            default:
                // identify pages that include the updated item and delete the cached files
                // nothing fancy yet, just flush it out
                $cacheKey = "$modname-";
                if (xarOutputCache::isPageCacheEnabled()) {
                    xarPageCache::flushCached($cacheKey);
                }
                // since we're modifying the config, we might get a new admin menulink
                if (xarOutputCache::isBlockCacheEnabled()) {
                    xarBlockCache::flushCached('base-block');
                }
                break;
        }

        if ($this->mod()->getVar('AutoRegenSessionless')) {
            CacheScheduler::regenstatic();
        }

        // Return the extra info
        return $extrainfo;
    }
}
