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
 * @extends MethodClass<AdminApi>
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
        if (!$this->checkAccess('AdminXarCache')) {
            return $menulinks;
        }

        $menulinks[] = ['url'   => $this->getUrl('admin', 'flushcache'),
            'title' => $this->translate('Flush the output cache of xarCache'),
            'label' => $this->translate('Flush Cache'), ];

        if (xarCache::isOutputCacheEnabled()) {
            if (xarOutputCache::isPageCacheEnabled()) {
                $menulinks[] = ['url'   => $this->getUrl('admin', 'pages'),
                    'title' => $this->translate('Configure the caching options for pages'),
                    'label' => $this->translate('Page Caching'), ];
            }
            if (xarOutputCache::isBlockCacheEnabled()) {
                $menulinks[] = ['url'   => $this->getUrl('admin', 'blocks'),
                    'title' => $this->translate('Configure the caching options for each block'),
                    'label' => $this->translate('Block Caching'), ];
            }
            if (xarOutputCache::isModuleCacheEnabled()) {
                $menulinks[] = ['url'   => $this->getUrl('admin', 'modules'),
                    'title' => $this->translate('Configure the caching options for modules'),
                    'label' => $this->translate('Module Caching'), ];
            }
            if (xarOutputCache::isObjectCacheEnabled()) {
                $menulinks[] = ['url'   => $this->getUrl('admin', 'objects'),
                    'title' => $this->translate('Configure the caching options for objects'),
                    'label' => $this->translate('Object Caching'), ];
            }
        }
        /*
            if (xarCache::isQueryCacheEnabled()) {
                $menulinks[] = Array('url'   => $this->getUrl('admin', 'queries'),
                                     'title' => $this->translate('Configure the caching options for queries'),
                                     'label' => $this->translate('Query Caching'));
            }
            if (xarCache::isVariableCacheEnabled()) {
                $menulinks[] = Array('url'   => $this->getUrl('admin', 'variables'),
                                     'title' => $this->translate('Configure the caching options for variables'),
                                     'label' => $this->translate('Variable Caching'));
            }
        */
        $menulinks[] = ['url'   => $this->getUrl('admin', 'stats'),
            'title' => $this->translate('View cache statistics'),
            'label' => $this->translate('View Statistics'), ];
        $menulinks[] = ['url'   => $this->getUrl('admin', 'modifyconfig'),
            'title' => $this->translate('Modify the xarCache configuration'),
            'label' => $this->translate('Modify Config'), ];

        return $menulinks;
    }
}
