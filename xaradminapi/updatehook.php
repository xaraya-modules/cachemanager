<?php
/**
 * Update hooks
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage CacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.cachemanager.class.hooks');
use Xaraya\Modules\CacheManager\CacheHooks;

/**
 * update entry for a module item - hook for ('item','update','API')
 * Optional $extrainfo['cachemanager_remark'] from arguments, or 'cachemanager_remark' from input
 *
 * @uses CacheHooks::updatehook()
 * @param array $args with mandatory arguments:
 * - int   $args['objectid'] ID of the object
 * - array $args['extrainfo'] extra information
 * @return array updated extrainfo array
 */
function cachemanager_adminapi_updatehook($args, $context = null)
{
    return CacheHooks::updatehook($args, $context);
}
