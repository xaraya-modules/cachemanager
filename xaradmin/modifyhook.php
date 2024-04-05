<?php
/**
 * Modify hook
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
 * modify an entry for a module item - hook for ('item','modify','GUI')
 *
 * @uses CacheHooks::modifyhook()
 * @param array $args with mandatory arguments:
 * - int   $args['objectid'] ID of the object
 * - array $args['extrainfo'] extra information
 * @return string hook output in HTML
 */
function cachemanager_admin_modifyhook($args, $context = null)
{
    return CacheHooks::modifyhook($args, $context);
}
