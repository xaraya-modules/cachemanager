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
use xarModuleCache;
use xarBlockCache;
use xarObjectCache;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * cachemanager adminapi createhook function
 * @extends MethodClass<AdminApi>
 */
class CreatehookMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * flush the appropriate cache when a module item is created- hook for ('item','create','API')
     * @param array<mixed> $args with mandatory arguments:
     * - int   $args['objectid'] ID of the object
     * - array $args['extrainfo'] extra information
     * @return array updated extrainfo array
     * @see AdminApi::createhook()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (!isset($objectid) || !is_numeric($objectid)) {
            $msg = $this->ml(
                'Invalid #(1) for #(2) function #(3)() in module #(4)',
                'object ID',
                'admin',
                'createhook',
                'cachemanager'
            );
            throw new BadParameterException(null, $msg);
        }
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
                'createhook',
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
            case 'blocks':
                // blocks could be anywhere, we're not smart enough not know exactly where yet
                // so just flush all pages
                if (xarOutputCache::isPageCacheEnabled()) {
                    xarPageCache::flushCached('');
                }
                break;
            case 'privileges': // fall-through all modules that should flush the entire cache
            case 'roles':
                // if security changes, flush everything, just in case.
                if (xarOutputCache::isPageCacheEnabled()) {
                    xarPageCache::flushCached('');
                }
                if (xarOutputCache::isBlockCacheEnabled()) {
                    xarBlockCache::flushCached('');
                }
                break;
            case 'articles':
                if (isset($extrainfo['status']) && $extrainfo['status'] == 0) {
                    break;
                }
                if (xarOutputCache::isPageCacheEnabled()) {
                    xarPageCache::flushCached('articles-');
                    // a status update might mean a new menulink and new base homepage
                    xarPageCache::flushCached('base');
                }
                if (xarOutputCache::isBlockCacheEnabled()) {
                    // a status update might mean a new menulink and new base homepage
                    xarBlockCache::flushCached('base');
                }
                break;
            case 'dynamicdata':
                // get the objectname
                sys::import('modules.dynamicdata.class.objects.descriptor');
                $objectinfo = $this->data()->getObjectID(['moduleid'  => $modid,
                    'itemtype' => $itemtype, ]);
                // CHECKME: how do we know if we need to e.g. flush dyn_example pages here ?
                // flush dynamicdata and objecturl pages
                if (xarOutputCache::isPageCacheEnabled()) {
                    xarPageCache::flushCached('dynamicdata-');
                    if (!empty($objectinfo) && !empty($objectinfo['name'])) {
                        xarPageCache::flushCached('objecturl-' . $objectinfo['name'] . '-');
                    }
                }
                // CHECKME: how do we know if we need to e.g. flush dyn_example module here ?
                // flush dynamicdata module
                if (xarOutputCache::isModuleCacheEnabled()) {
                    xarModuleCache::flushCached('dynamicdata-');
                }
                // flush objects by name, e.g. dyn_example
                if (xarOutputCache::isObjectCacheEnabled()) {
                    if (!empty($objectinfo) && !empty($objectinfo['name'])) {
                        xarObjectCache::flushCached($objectinfo['name'] . '-');
                    }
                }
                break;
            case 'autolinks': // fall-through all hooked utility modules that are admin modified
            case 'categories': // keep falling through
            case 'html': // keep falling through
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
            default:
                // identify pages that include the updated item and delete the cached files
                // nothing fancy yet, just flush it out
                $cacheKey = "$modname-";
                if (xarOutputCache::isPageCacheEnabled()) {
                    xarPageCache::flushCached($cacheKey);
                }
                // a new item might mean a new menulink
                if (xarOutputCache::isBlockCacheEnabled()) {
                    xarBlockCache::flushCached('base-');
                }
                break;
        }

        if ($this->mod()->getVar('AutoRegenSessionless')) {
            CacheScheduler::regenstatic();
        }

        return $extrainfo;
    }
}
