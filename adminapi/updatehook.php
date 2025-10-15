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
use Xaraya\Modules\CacheManager\CacheUtility;
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
 * cachemanager adminapi updatehook function
 * @extends MethodClass<AdminApi>
 */
class UpdatehookMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * update entry for a module item - hook for ('item','update','API')
     * Optional $extrainfo['cachemanager_remark'] from arguments, or 'cachemanager_remark' from input
     * @param array<mixed> $args with mandatory arguments:
     * - int   $args['objectid'] ID of the object
     * - array $args['extrainfo'] extra information
     * @return array updated extrainfo array
     * @see AdminApi::updatehook()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (!isset($objectid) || !is_numeric($objectid)) {
            $msg = $this->ml(
                'Invalid #(1) for #(2) function #(3)() in module #(4)',
                'object ID',
                'admin',
                'updatehook',
                'cachemanager'
            );
            throw new BadParameterException(null, $msg);
        }
        if (!isset($extrainfo) || !is_array($extrainfo)) {
            $extrainfo = [];
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
            case 'blocks':
                // Get the output cache directory so you can access it even if output caching is disabled
                $outputCacheDir = xarCache::getOutputCacheDir();

                // first, if authorized, save the new settings
                // (don't check if output caching is enabled here so config options can be tweaked
                //  even when output caching has been temporarily disabled)
                if (xarOutputCache::isBlockCacheEnabled() &&
                    $this->sec()->checkAccess('AdminXarCache', 0)) {
                    $this->var()->find('nocache', $nocache, 'isset', 0);
                    $this->var()->find('pageshared', $pageshared, 'isset', 0);
                    $this->var()->find('usershared', $usershared, 'isset', 0);
                    $this->var()->find('cacheexpire', $cacheexpire, 'str:1:9', null);

                    if (empty($nocache)) {
                        $nocache = 0;
                    }
                    if (empty($pageshared)) {
                        $pageshared = 0;
                    }
                    if (!isset($cacheexpire)) {
                        $cacheexpire = null;
                    }
                    if (!empty($cacheexpire)) {
                        $cacheexpire = CacheUtility::convertToSeconds($cacheexpire);
                    }

                    $systemPrefix = $this->db()->getPrefix();
                    $blocksettings = $systemPrefix . '_cache_blocks';
                    $dbconn = $this->db()->getConn();
                    $query = "SELECT nocache
                                FROM $blocksettings WHERE blockinstance_id = $objectid ";
                    $result = & $dbconn->Execute($query);
                    if (count($result) > 0) {
                        $query = "DELETE FROM
                                 $blocksettings WHERE blockinstance_id = $objectid ";
                        $result = & $dbconn->Execute($query);
                    }
                    $query = "INSERT INTO $blocksettings (blockinstance_id,
                                                          nocache,
                                                          page,
                                                          theuser,
                                                          expire)
                                VALUES (?,?,?,?,?)";
                    $bindvars = [$objectid, $nocache, $pageshared, $usershared, $cacheexpire];
                    $result = & $dbconn->Execute($query, $bindvars);
                }

                // blocks could be anywhere, we're not smart enough not know exactly where yet
                // so just flush all pages
                if (xarCache::isOutputCacheEnabled() && xarOutputCache::isPageCacheEnabled()) {
                    xarPageCache::flushCached('');
                }
                // and flush the block
                // FIXME: we can't filter on the middle of the key, only on the start of it
                $cacheKey = "-blockid" . $objectid;
                if (xarCache::isOutputCacheEnabled() && xarOutputCache::isBlockCacheEnabled()) {
                    xarBlockCache::flushCached('');
                }
                break;
            case 'articles':
                if (xarCache::isOutputCacheEnabled() && xarOutputCache::isPageCacheEnabled()) {
                    xarPageCache::flushCached('articles-');
                    // a status update might mean a new menulink and new base homepage
                    xarPageCache::flushCached('base');
                }
                if (xarCache::isOutputCacheEnabled() && xarOutputCache::isBlockCacheEnabled()) {
                    // a status update might mean a new menulink and new base homepage
                    xarBlockCache::flushCached('base');
                }
                break;
            case 'privileges': // fall-through all modules that should flush the entire cache
            case 'roles':
                // if security changes, flush everything, just in case.
                if (xarCache::isOutputCacheEnabled() && xarOutputCache::isPageCacheEnabled()) {
                    xarPageCache::flushCached('');
                }
                if (xarCache::isOutputCacheEnabled() && xarOutputCache::isBlockCacheEnabled()) {
                    xarBlockCache::flushCached('');
                }
                break;
            case 'dynamicdata':
                // get the objectname
                sys::import('modules.dynamicdata.class.objects.descriptor');
                $objectinfo = $this->data()->getObjectID(['moduleid'  => $modid,
                    'itemtype' => $itemtype, ]);
                // CHECKME: how do we know if we need to e.g. flush dyn_example pages here ?
                // flush dynamicdata and objecturl pages
                if (xarCache::isOutputCacheEnabled() && xarOutputCache::isPageCacheEnabled()) {
                    xarPageCache::flushCached('dynamicdata-');
                    if (!empty($objectinfo) && !empty($objectinfo['name'])) {
                        xarPageCache::flushCached('objecturl-' . $objectinfo['name'] . '-');
                    }
                }
                // CHECKME: how do we know if we need to e.g. flush dyn_example module here ?
                // flush dynamicdata module
                if (xarCache::isOutputCacheEnabled() && xarOutputCache::isModuleCacheEnabled()) {
                    xarModuleCache::flushCached('dynamicdata-');
                }
                // flush objects by name, e.g. dyn_example
                if (xarCache::isOutputCacheEnabled() && xarOutputCache::isObjectCacheEnabled()) {
                    if (!empty($objectinfo) && !empty($objectinfo['name'])) {
                        xarObjectCache::flushCached($objectinfo['name'] . '-');
                    }
                }
                break;
            case 'autolinks': // fall-through all hooked utility modules that are admin modified
            case 'categories': // keep falling through
            case 'keywords': // keep falling through
            case 'html': // keep falling through
                // delete cachekey of each module autolinks is hooked to.
                if (xarCache::isOutputCacheEnabled() && xarOutputCache::isPageCacheEnabled()) {
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
                if (xarCache::isOutputCacheEnabled() && xarOutputCache::isPageCacheEnabled()) {
                    xarPageCache::flushCached($cacheKey);
                }
                break;
        }

        if (xarCache::isOutputCacheEnabled() && $this->mod()->getVar('AutoRegenSessionless')) {
            CacheScheduler::regenstatic();
        }

        // Return the extra info
        return $extrainfo;
    }
}
