<?php
/**
 * Config object caching
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage CacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.cachemanager.class.config.objectcache');
use Xaraya\Modules\CacheManager\Config\ObjectCache;

/**
 * configure object caching
 * @uses ObjectCache::modifyConfig()
 * @return array
 */
function cachemanager_admin_objects(array $args = [], $context = null)
{
    return ObjectCache::modifyConfig($args);
}
