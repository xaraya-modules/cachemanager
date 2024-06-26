<?php
/**
 * Regenerate the page output cache of URLs in sessionless list
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage CacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.cachemanager.class.hooks');
use Xaraya\Modules\CacheManager\CacheHooks;

/**
 * regenerate the page output cache of URLs in the session-less list
 * @author jsb
 *
 * @uses CacheHooks::regenstatic()
 * @return void
 */
function cachemanager_adminapi_regenstatic($nolimit = null)
{
    CacheHooks::regenstatic($nolimit);
}
