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
use Xaraya\Modules\CacheManager\CacheUtility;
use Xaraya\Modules\MethodClass;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * cachemanager adminapi convertseconds function
 * @extends MethodClass<AdminApi>
 */
class ConvertsecondsMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Update the configuration parameters of the module based on data from the modification form
     * @author Jon Haworth
     * @author jsb <jsb@xaraya.com>
     * @access public
     * @uses CacheUtility::convertseconds()
     * @param array<mixed> $args
     * @var string $args['starttime'] (seconds or hh:mm:ss)
     * @var string $args['direction'] (from or to)
     * @return string $convertedtime (hh:mm:ss or seconds)
     */
    public function __invoke(array $args = [])
    {
        return CacheUtility::convertseconds($args['starttime'] ?? 0, $args['direction'] ?? 'from');
    }
}
