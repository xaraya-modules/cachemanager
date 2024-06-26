<?php
/**
 * Main
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage CacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
/**
 * the main administration function
 *
 * @author jsb | mikespub
 * @access public
 * @return true|void on success or void on falure
 */
function cachemanager_admin_main(array $args = [], $context = null)
{
    // Security Check
    if (!xarSecurity::check('AdminXarCache')) {
        return;
    }

    xarController::redirect(xarController::URL('cachemanager', 'admin', 'modifyconfig'), null, $context);
    // success
    return true;
}
