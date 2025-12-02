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

use Xaraya\Modules\CacheManager\AdminGui;
use Xaraya\Modules\CacheManager\AdminApi;
use Xaraya\Modules\CacheManager\CacheManager;
use Xaraya\Modules\CacheManager\CacheUtility;
use Xaraya\Modules\MethodClass;

/**
 * cachemanager admin modifyconfig function
 * @extends MethodClass<AdminGui>
 */
class ModifyconfigMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Prep the configuration parameters of the module for the modification form
     * @author jsb | mikespub
     * @access public
     * @return array|void $data (array of values for admin modify template) on success or false on failure
     * @see AdminGui::modifyconfig()
     */
    public function __invoke(array $args = [])
    {
        // Security Check
        if (!$this->sec()->checkAccess('AdminXarCache')) {
            return;
        }

        /** @var AdminApi $adminapi */
        $adminapi = $this->adminapi();

        $data = [];

        // get cache status
        $data['status'] = $adminapi->getstatus();

        $data['CookieName'] =  ($this->config()->getVar('Site.Session.CookieName') != '') ? $this->config()->getVar('Site.Session.CookieName') : 'XARAYASID';
        $data['cookieupdatelink'] = $this->ctl()->getModuleURL('base', 'admin', 'modifyconfig', ['tab' => 'security']);
        $data['defaultlocale'] = $this->mls()->getSiteLocale();
        $data['localeupdatelink'] = $this->ctl()->getModuleURL('base', 'admin', 'modifyconfig', ['tab' => 'locales']);

        // get the caching config settings from the config file
        $data['settings'] = CacheManager::get_config(
            ['from' => 'file', 'tpl_prep' => true]
        );

        // set some default values
        if (!isset($data['settings']['OutputSizeLimit'])) {
            $data['settings']['OutputSizeLimit'] = 2097152;
        }
        if (!isset($data['settings']['OutputCookieName'])) {
            $data['settings']['OutputCookieName'] = $data['CookieName'];
        }
        if (!isset($data['settings']['OutputDefaultLocale'])) {
            $data['settings']['OutputDefaultLocale'] = $data['defaultlocale'];
        }
        if (!isset($data['settings']['PageTimeExpiration'])) {
            $data['settings']['PageTimeExpiration'] = 1800;
        }
        if (!isset($data['settings']['PageDisplayView'])) {
            $data['settings']['PageDisplayView'] = 0;
        }
        if (!isset($data['settings']['PageViewTime'])) {
            $data['settings']['PageViewTime'] = 0;
        }
        if (!isset($data['settings']['PageExpireHeader'])) {
            $data['settings']['PageExpireHeader'] = 1;
        }
        if (!isset($data['settings']['PageCacheStorage'])) {
            $data['settings']['PageCacheStorage'] = 'filesystem';
        }
        if (!isset($data['settings']['PageLogFile'])) {
            $data['settings']['PageLogFile'] = '';
        }
        if (!isset($data['settings']['PageSizeLimit'])) {
            $data['settings']['PageSizeLimit'] = $data['settings']['OutputSizeLimit'];
        }

        if (!isset($data['settings']['BlockTimeExpiration'])) {
            $data['settings']['BlockTimeExpiration'] = 7200;
        }
        if (!isset($data['settings']['BlockCacheStorage'])) {
            $data['settings']['BlockCacheStorage'] = 'filesystem';
        }
        if (!isset($data['settings']['BlockLogFile'])) {
            $data['settings']['BlockLogFile'] = '';
        }
        if (!isset($data['settings']['BlockSizeLimit'])) {
            $data['settings']['BlockSizeLimit'] = $data['settings']['OutputSizeLimit'];
        }

        if (!isset($data['settings']['ModuleTimeExpiration'])) {
            $data['settings']['ModuleTimeExpiration'] = 7200;
        }
        if (!isset($data['settings']['ModuleCacheStorage'])) {
            $data['settings']['ModuleCacheStorage'] = 'filesystem';
        }
        if (!isset($data['settings']['ModuleLogFile'])) {
            $data['settings']['ModuleLogFile'] = '';
        }
        if (!isset($data['settings']['ModuleSizeLimit'])) {
            $data['settings']['ModuleSizeLimit'] = $data['settings']['OutputSizeLimit'];
        }
        // set new cache defaults for module functions
        if (empty($data['settings']['ModuleCacheFunctions'])) {
            $data['settings']['ModuleCacheFunctions'] = ['main' => 1, 'view' => 1, 'display' => 0];
        }
        $this->mod()->setVar('DefaultModuleCacheFunctions', serialize($data['settings']['ModuleCacheFunctions']));

        if (!isset($data['settings']['ObjectTimeExpiration'])) {
            $data['settings']['ObjectTimeExpiration'] = 7200;
        }
        if (!isset($data['settings']['ObjectCacheStorage'])) {
            $data['settings']['ObjectCacheStorage'] = 'filesystem';
        }
        if (!isset($data['settings']['ObjectLogFile'])) {
            $data['settings']['ObjectLogFile'] = '';
        }
        if (!isset($data['settings']['ObjectSizeLimit'])) {
            $data['settings']['ObjectSizeLimit'] = $data['settings']['OutputSizeLimit'];
        }
        // set new cache defaults for object methods
        if (empty($data['settings']['ObjectCacheMethods'])) {
            $data['settings']['ObjectCacheMethods'] = ['view' => 1, 'display' => 1];
        }
        $this->mod()->setVar('DefaultObjectCacheMethods', serialize($data['settings']['ObjectCacheMethods']));

        // convert the size limit from bytes to megabytes
        $data['settings']['OutputSizeLimit'] /= 1048576;
        $data['settings']['PageSizeLimit'] /= 1048576;
        $data['settings']['BlockSizeLimit'] /= 1048576;
        $data['settings']['ModuleSizeLimit'] /= 1048576;
        $data['settings']['ObjectSizeLimit'] /= 1048576;

        // reformat seconds as hh:mm:ss
        $data['settings']['PageTimeExpiration'] = CacheUtility::convertFromSeconds($data['settings']['PageTimeExpiration']);
        $data['settings']['BlockTimeExpiration'] = CacheUtility::convertFromSeconds($data['settings']['BlockTimeExpiration']);
        $data['settings']['ModuleTimeExpiration'] = CacheUtility::convertFromSeconds($data['settings']['ModuleTimeExpiration']);
        $data['settings']['ObjectTimeExpiration'] = CacheUtility::convertFromSeconds($data['settings']['ObjectTimeExpiration']);

        // get the themes list
        $filter['Class'] = 2;
        $data['themes'] = $this->mod()->apiFunc(
            'themes',
            'admin',
            'getlist',
            $filter
        );

        // get the storage types supported on this server
        $data['storagetypes'] = $adminapi->getstoragetypes();

        $data['authid'] = $this->sec()->genAuthKey();
        return $data;
    }
}
