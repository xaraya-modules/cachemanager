<?php
/**
 * Get queries caching config
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
 * get configuration of query caching for expensive queries
 *
 * @uses QueryCache::getConfig()
 * @return array of query caching configurations
 */
function cachemanager_adminapi_getqueries(array $args = [], $context = null)
{
    extract($args);

    // Get all query cache settings
    return QueryCache::getConfig();
}
