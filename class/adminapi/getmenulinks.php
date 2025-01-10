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

use Xaraya\Modules\MethodClass;
use xarSecurity;
use xarController;
use xarMLS;
use xarCache;
use xarOutputCache;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * cachemanager adminapi getmenulinks function
 */
class GetmenulinksMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * utility function pass individual menu items to the main menu
     * @author jsb| mikespub
     * @return array containing the menulinks for the main menu items.
     */
    public function __invoke(array $args = [])
    {
        $menulinks = [];

        // Security Check
        if (!xarSecurity::check('AdminXarCache')) {
            return $menulinks;
        }

        $menulinks[] = ['url'   => xarController::URL(
            'cachemanager',
            'admin',
            'flushcache'
        ),
            'title' => xarMLS::translate('Flush the output cache of xarCache'),
            'label' => xarMLS::translate('Flush Cache'), ];

        if (xarCache::isOutputCacheEnabled()) {
            if (xarOutputCache::isPageCacheEnabled()) {
                $menulinks[] = ['url'   => xarController::URL(
                    'cachemanager',
                    'admin',
                    'pages'
                ),
                    'title' => xarMLS::translate('Configure the caching options for pages'),
                    'label' => xarMLS::translate('Page Caching'), ];
            }
            if (xarOutputCache::isBlockCacheEnabled()) {
                $menulinks[] = ['url'   => xarController::URL(
                    'cachemanager',
                    'admin',
                    'blocks'
                ),
                    'title' => xarMLS::translate('Configure the caching options for each block'),
                    'label' => xarMLS::translate('Block Caching'), ];
            }
            if (xarOutputCache::isModuleCacheEnabled()) {
                $menulinks[] = ['url'   => xarController::URL(
                    'cachemanager',
                    'admin',
                    'modules'
                ),
                    'title' => xarMLS::translate('Configure the caching options for modules'),
                    'label' => xarMLS::translate('Module Caching'), ];
            }
            if (xarOutputCache::isObjectCacheEnabled()) {
                $menulinks[] = ['url'   => xarController::URL(
                    'cachemanager',
                    'admin',
                    'objects'
                ),
                    'title' => xarMLS::translate('Configure the caching options for objects'),
                    'label' => xarMLS::translate('Object Caching'), ];
            }
        }
        /*
            if (xarCache::isQueryCacheEnabled()) {
                $menulinks[] = Array('url'   => xarController::URL('cachemanager',
                                                          'admin',
                                                          'queries'),
                                     'title' => xarMLS::translate('Configure the caching options for queries'),
                                     'label' => xarMLS::translate('Query Caching'));
            }
            if (xarCache::isVariableCacheEnabled()) {
                $menulinks[] = Array('url'   => xarController::URL('cachemanager',
                                                          'admin',
                                                          'variables'),
                                     'title' => xarMLS::translate('Configure the caching options for variables'),
                                     'label' => xarMLS::translate('Variable Caching'));
            }
        */
        $menulinks[] = ['url'   => xarController::URL(
            'cachemanager',
            'admin',
            'stats'
        ),
            'title' => xarMLS::translate('View cache statistics'),
            'label' => xarMLS::translate('View Statistics'), ];
        $menulinks[] = ['url'   => xarController::URL(
            'cachemanager',
            'admin',
            'modifyconfig'
        ),
            'title' => xarMLS::translate('Modify the xarCache configuration'),
            'label' => xarMLS::translate('Modify Config'), ];

        return $menulinks;
    }
}
