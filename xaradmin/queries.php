<?php
/**
 * Queries
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage CacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.cachemanager.class.config.querycache');
use Xaraya\Modules\CacheManager\Config\QueryCache;

/**
 * configure query caching (TODO)
 * @uses QueryCache::modifyConfig()
 * @return array
 */
function cachemanager_admin_queries(array $args = [], $context = null)
{
    return QueryCache::modifyConfig($args);
}
