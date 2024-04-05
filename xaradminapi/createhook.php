<?php
/**
 * Flush the appropriate cache
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
 * flush the appropriate cache when a module item is created- hook for ('item','create','API')
 *
 * @uses CacheHooks::createhook()
 * @param array $args with mandatory arguments:
 * - int   $args['objectid'] ID of the object
 * - array $args['extrainfo'] extra information
 * @return array updated extrainfo array
 */
function cachemanager_adminapi_createhook($args, $context = null)
{
    return CacheHooks::createhook($args, $context);
}
