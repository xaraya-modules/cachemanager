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
use sys;

sys::import('xaraya.modules.method');

/**
 * cachemanager adminapi getcachedirs function
 * @extends MethodClass<AdminApi>
 */
class GetcachedirsMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * construct an array of output cache subdirectories
     * @param mixed $dir directory to start the search for subdirectories in
     * @return array sorted array of cache sub directories, with key set to directory name and value set to path
     * @todo do not include empty directories in the array
     * @see AdminApi::getcachedirs()
     */
    public function __invoke($dir = false)
    {
        $cachedirs = [];

        if (!empty($dir) && is_dir($dir)) {
            if (substr($dir, -1) != "/") {
                $dir .= "/";
            }
            if ($dirId = opendir($dir)) {
                while (($item = readdir($dirId)) !== false) {
                    if ($item[0] != '.') {
                        if (is_dir($dir . $item)) {
                            $cachedirs[$item] = $dir . $item;
                            $cachedirs = array_merge($cachedirs, $this->__invoke($dir . $item));
                        }
                    }
                }
                closedir($dirId);
            }
        }
        asort($cachedirs);
        return $cachedirs;
    }
}
