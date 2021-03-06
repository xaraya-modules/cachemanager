<?php
/**
 * Queries
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
/**
 * configure query caching (TODO)
 * @return array
 */
function xarcachemanager_admin_queries($args)
{
    extract($args);

    if (!xarSecurity::check('AdminXarCache')) {
        return;
    }

    $data = [];

    xarVar::fetch('submit', 'str', $submit, '');
    if (!empty($submit)) {
        // Confirm authorisation code
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        xarVar::fetch('expire', 'isset', $expire, []);
        foreach ($expire as $module => $querylist) {
            if ($module == 'core') {
                // define some way to store configuration options for the core
                foreach ($querylist as $query => $time) {
                }
            } elseif (xarMod::isAvailable($module)) {
                // stored in module variables (for now ?)
                foreach ($querylist as $query => $time) {
                    if (empty($time) || !is_numeric($time)) {
                        xarModVars::set($module, 'cache.'.$query, 0);
                    } else {
                        xarModVars::set($module, 'cache.'.$query, $time);
                    }
                }
            }
        }
        xarResponse::Redirect(xarController::URL('xarcachemanager', 'admin', 'queries'));
        return true;
    }

    // Get some query caching configurations
    $data['queries'] = xarMod::apiFunc('xarcachemanager', 'admin', 'getqueries');

    $data['authid'] = xarSec::genAuthKey();
    return $data;
}
