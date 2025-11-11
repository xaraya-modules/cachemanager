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
     * @see AdminApi::getmenulinks()
     */
    public function __invoke(array $args = [])
    {
        $menulinks = [];

        // Security Check
        if (!$this->sec()->checkAccess('AdminXarCache')) {
            return $menulinks;
        }

        $menulinks[] = ['url'   => $this->mod()->getURL('admin', 'flushcache'),
            'title' => $this->ml('Flush the output cache of xarCache'),
            'label' => $this->ml('Flush Cache'), ];

        if ($this->cache()->withOutput()) {
            if ($this->cache()->withPages()) {
                $menulinks[] = ['url'   => $this->mod()->getURL('admin', 'pages'),
                    'title' => $this->ml('Configure the caching options for pages'),
                    'label' => $this->ml('Page Caching'), ];
            }
            if ($this->cache()->withBlocks()) {
                $menulinks[] = ['url'   => $this->mod()->getURL('admin', 'blocks'),
                    'title' => $this->ml('Configure the caching options for each block'),
                    'label' => $this->ml('Block Caching'), ];
            }
            if ($this->cache()->withModules()) {
                $menulinks[] = ['url'   => $this->mod()->getURL('admin', 'modules'),
                    'title' => $this->ml('Configure the caching options for modules'),
                    'label' => $this->ml('Module Caching'), ];
            }
            if ($this->cache()->withObjects()) {
                $menulinks[] = ['url'   => $this->mod()->getURL('admin', 'objects'),
                    'title' => $this->ml('Configure the caching options for objects'),
                    'label' => $this->ml('Object Caching'), ];
            }
        }
        /*
            if ($this->cache()->withQueries()) {
                $menulinks[] = Array('url'   => $this->mod()->getURL('admin', 'queries'),
                                     'title' => $this->ml('Configure the caching options for queries'),
                                     'label' => $this->ml('Query Caching'));
            }
            if ($this->cache()->withVariables()) {
                $menulinks[] = Array('url'   => $this->mod()->getURL('admin', 'variables'),
                                     'title' => $this->ml('Configure the caching options for variables'),
                                     'label' => $this->ml('Variable Caching'));
            }
        */
        $menulinks[] = ['url'   => $this->mod()->getURL('admin', 'stats'),
            'title' => $this->ml('View cache statistics'),
            'label' => $this->ml('View Statistics'), ];
        $menulinks[] = ['url'   => $this->mod()->getURL('admin', 'modifyconfig'),
            'title' => $this->ml('Modify the xarCache configuration'),
            'label' => $this->ml('Modify Config'), ];

        return $menulinks;
    }
}
