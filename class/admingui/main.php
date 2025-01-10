<?php

/**
 * @package modules\cachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\CacheManager\AdminGui;

use Xaraya\Modules\MethodClass;
use xarSecurity;
use xarController;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * cachemanager admin main function
 */
class MainMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * the main administration function
     * @author jsb | mikespub
     * @access public
     * @return true|void on success or void on falure
     */
    public function __invoke(array $args = [])
    {
        // Security Check
        if (!xarSecurity::check('AdminXarCache')) {
            return;
        }

        xarController::redirect(xarController::URL('cachemanager', 'admin', 'modifyconfig'), null, $this->getContext());
        // success
        return true;
    }
}
