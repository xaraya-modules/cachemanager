<?php

/**
 * @package modules\cachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.6.2
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\CacheManager\AdminGui;

use Xaraya\Modules\CacheManager\AdminGui;
use Xaraya\Modules\CacheManager\CacheUtility;
use Xaraya\Modules\MethodClass;
use BadParameterException;

/**
 * cachemanager admin modifyhook function
 * @extends MethodClass<AdminGui>
 */
class ModifyhookMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * modify an entry for a module item - hook for ('item','modify','GUI')
     * @param array<mixed> $args with mandatory arguments:
     * - int   $args['objectid'] ID of the object
     * - array $args['extrainfo'] extra information
     * @return string hook output in HTML
     * @see AdminGui::modifyhook()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if (!$this->sec()->checkAccess('AdminXarCache', 0)) {
            return '';
        }

        // Get the output cache directory so you can access it even if output caching is disabled
        $outputCacheDir = $this->cache()->getOutputCacheDir();

        // only display modify hooks if block level output caching has been enabled
        // (don't check if output caching is enabled here so config options can be tweaked
        //  even when output caching has been temporarily disabled)
        if (!$this->cache()->withBlocks()) {
            return '';
        }

        if (!isset($extrainfo)) {
            $msg = $this->ml(
                'Invalid #(1) for #(2) function #(3)() in module #(4)',
                'extrainfo',
                'admin',
                'modifyhook',
                'changelog'
            );
            throw new BadParameterException(null, $msg);
        }

        if (!isset($objectid) || !is_numeric($objectid)) {
            $msg = $this->ml(
                'Invalid #(1) for #(2) function #(3)() in module #(4)',
                'object ID',
                'admin',
                'modifyhook',
                'changelog'
            );
            throw new BadParameterException(null, $msg);
        }

        // When called via hooks, the module name may be empty, so we get it from
        // the current module
        if (empty($extrainfo['module'])) {
            $modname = $this->req()->getModule();
        } else {
            $modname = $extrainfo['module'];
        }

        // we are only interested in the config of block output caching for now
        if ($modname !== 'blocks') {
            return '';
        }

        $modid = $this->mod()->getRegID($modname);
        if (empty($modid)) {
            $msg = $this->ml(
                'Invalid #(1) for #(2) function #(3)() in module #(4)',
                'module name',
                'admin',
                'modifyhook',
                'changelog'
            );
            throw new BadParameterException(null, $msg);
        }

        if (!empty($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
            $itemtype = $extrainfo['itemtype'];
        } else {
            $itemtype = 0;
        }

        if (!empty($extrainfo['itemid']) && is_numeric($extrainfo['itemid'])) {
            $itemid = $extrainfo['itemid'];
        } else {
            $itemid = $objectid;
        }

        $systemPrefix = $this->db()->getPrefix();
        $blocksettings = $systemPrefix . '_cache_blocks';
        $dbconn = $this->db()->getConn();
        $query = "SELECT nocache,
                 page,
                 theuser,
                 expire
                 FROM $blocksettings WHERE blockinstance_id = $itemid ";
        $result = & $dbconn->Execute($query);
        if ($result && $result->first()) {
            [$noCache, $pageShared, $userShared, $blockCacheExpireTime] = $result->fields;
        } else {
            $noCache = 0;
            $pageShared = 0;
            $userShared = 0;
            $blockCacheExpireTime = null;
            // Get the instance details.
            $instance = $this->mod()->apiFunc('blocks', 'user', 'get', ['bid' => $itemid]);
            // Try loading some defaults from the block init array (cfr. articles/random)
            if (!empty($instance) && !empty($instance['module']) && !empty($instance['type'])) {
                $initresult = $this->mod()->apiFunc(
                    'blocks',
                    'user',
                    'read_type_init',
                    ['module' => $instance['module'],
                        'type' => $instance['type'], ]
                );
                if (!empty($initresult) && is_array($initresult)) {
                    if (isset($initresult['nocache'])) {
                        $noCache = $initresult['nocache'];
                    }
                    if (isset($initresult['pageshared'])) {
                        $pageShared = $initresult['pageshared'];
                    }
                    if (isset($initresult['usershared'])) {
                        $userShared = $initresult['usershared'];
                    }
                    if (isset($initresult['cacheexpire'])) {
                        $blockCacheExpireTime = $initresult['cacheexpire'];
                    }
                }
            }
        }
        if (!empty($blockCacheExpireTime)) {
            $blockCacheExpireTime = CacheUtility::convertFromSeconds($blockCacheExpireTime);
        }
        return $this->tpl()->module(
            'cachemanager',
            'admin',
            'modifyhook',
            ['noCache' => $noCache,
                'pageShared' => $pageShared,
                'userShared' => $userShared,
                'cacheExpire' => $blockCacheExpireTime, ]
        );
    }
}
