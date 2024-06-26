<?php
/**
 * Config module caching
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage CacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.cachemanager.class.config.modulecache');
use Xaraya\Modules\CacheManager\Config\ModuleCache;

/**
 * configure module caching
 * @uses ModuleCache::modifyConfig()
 * @return array
 */
function cachemanager_admin_modules(array $args = [], $context = null)
{
    return ModuleCache::modifyConfig($args);
}
