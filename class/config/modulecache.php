<?php

/**
 * Classes to manage config for the cache system of Xaraya
 *
 * @package modules\cachemanager
 * @subpackage cachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/182.html
 *
 * @author mikespub <mikespub@xaraya.com>
**/

namespace Xaraya\Modules\CacheManager\Config;

use xarCache;
use xarOutputCache;
use xarPageCache;
use xarVar;
use xarMod;
use xarModuleCache;
use sys;

sys::import('modules.cachemanager.class.config');
sys::import('modules.cachemanager.class.utility');
use Xaraya\Modules\CacheManager\CacheConfig;
use Xaraya\Modules\CacheManager\CacheUtility;

class ModuleCache extends CacheConfig
{
    public static function init(array $args = []) {}

    /**
     * configure module caching
     * @return array|void
     */
    public function modifyConfig($args)
    {
        extract($args);

        if (!$this->sec()->checkAccess('AdminXarCache')) {
            return;
        }

        $data = [];
        if (!xarCache::isOutputCacheEnabled() || !xarOutputCache::isModuleCacheEnabled()) {
            $data['modules'] = [];
            return $data;
        }

        $this->var()->get('submit', $submit, 'str', '');
        if (!empty($submit)) {
            // Confirm authorisation code
            if (!$this->sec()->confirmAuthKey()) {
                return;
            }

            $this->var()->get('docache', $docache, 'isset', []);
            $this->var()->get('usershared', $usershared, 'isset', []);
            $this->var()->get('params', $params, 'isset', []);
            $this->var()->get('cacheexpire', $cacheexpire, 'isset', []);

            $newmodules = [];
            // loop over something that should return values for every module
            foreach ($cacheexpire as $name => $expirelist) {
                $newmodules[$name] = [];
                foreach ($expirelist as $func => $expire) {
                    $newmodules[$name][$func] = [];
                    // flip from docache in template to nocache in settings
                    if (!empty($docache[$name]) && !empty($docache[$name][$func])) {
                        $newmodules[$name][$func]['nocache'] = 0;
                    } else {
                        $newmodules[$name][$func]['nocache'] = 1;
                    }
                    if (!empty($usershared[$name]) && !empty($usershared[$name][$func])) {
                        $newmodules[$name][$func]['usershared'] = intval($usershared[$name][$func]);
                    } else {
                        $newmodules[$name][$func]['usershared'] = 0;
                    }
                    if (!empty($params[$name]) && !empty($params[$name][$func])) {
                        $newmodules[$name][$func]['params'] = $params[$name][$func];
                    } else {
                        $newmodules[$name][$func]['params'] = '';
                    }
                    if (!empty($expire)) {
                        $expire = CacheUtility::convertToSeconds($expire);
                    } elseif ($expire === '0') {
                        $expire = 0;
                    } else {
                        $expire = null;
                    }
                    $newmodules[$name][$func]['cacheexpire'] = $expire;
                }
            }
            // save settings to modules in case cachemanager is removed later
            $this->mod('modules')->setVar('modulecache_settings', serialize($newmodules));

            // modules could be anywhere, we're not smart enough not know exactly where yet
            $key = '';
            // so just flush all pages
            if (xarOutputCache::isPageCacheEnabled()) {
                xarPageCache::flushCached($key);
            }
            // and flush the modules
            xarModuleCache::flushCached($key);
            if ($this->mod()->getVar('AutoRegenSessionless')) {
                $this->mod()->apiFunc('cachemanager', 'admin', 'regenstatic');
            }
        }

        // Get all module caching configurations
        $data['modules'] = $this->getConfig();

        $data['authid'] = $this->sec()->genAuthKey();
        return $data;
    }

    /**
     * get configuration of module caching for all modules
     *
     * @return array module caching configurations
     */
    public function getConfig()
    {
        // Get all module cache settings
        $modulesettings = [];
        $serialsettings = $this->mod('modules')->getVar('modulecache_settings');
        if (!empty($serialsettings)) {
            $modulesettings = unserialize($serialsettings);
        }

        // Get default module functions to cache
        $defaultmodulefunctions = unserialize((string) $this->mod()->getVar('DefaultModuleCacheFunctions'));

        // Get all modules
        $modules = $this->mod()->apiFunc('modules', 'admin', 'getlist');

        // Get all module functions (user GUI)
        $modulefunctions = [];
        foreach ($modules as $module) {
            $name = $module['name'];
            $modulefunctions[$name] = [];
            // find the caching configuration from the mapper file (if it exists)
            $mapperfile = realpath(sys::code() . 'modules/' . $module['directory'] . '/xarmapper.php');
            if ($mapperfile) {
                include $mapperfile;
                $funcname = $name . '_xarmapper';
                if (function_exists($funcname)) {
                    $xarmapper = $funcname();
                    if (!empty($xarmapper['user'])) {
                        // set the module functions
                        $modulefunctions[$name] = array_keys($xarmapper['user']);
                        // initialize the module settings from the mapper file if necessary
                        foreach ($modulefunctions[$name] as $func) {
                            if (empty($modulesettings[$name][$func]) && !empty($xarmapper['user'][$func])) {
                                $modulesettings[$name][$func] = $xarmapper['user'][$func];
                                // flip from docache in config to nocache in settings
                                if (!empty($defaultmodulefunctions[$func])) {
                                    $modulesettings[$name][$func]['nocache'] = 0;
                                } else {
                                    $modulesettings[$name][$func]['nocache'] = 1;
                                }
                            }
                        }
                    }
                }
                continue;
            }
            // find the user GUI functions
            $userdir = realpath(sys::code() . 'modules/' . $module['directory'] . '/xaruser/');
            if ($userdir && $dh = @opendir($userdir)) {
                while (($filename = @readdir($dh)) !== false) {
                    // Got a file or directory.
                    $thisfile = $userdir . '/' . $filename;
                    if (is_file($thisfile) && preg_match('/^(.+)\.php$/', $filename, $matches)) {
                        $func = $matches[1];
                        // add this module function to the list
                        $modulefunctions[$name][] = $func;
                        // initialize the module settings from the function file if necessary
                        if (empty($modulesettings[$name][$func])) {
                            $settings = [];
                            // try to find all xarVar::fetch() parameters in this function
                            $params = [];
                            $content = implode('', file($thisfile));
                            if (preg_match_all("/xarVar::fetch\(\s*'([^']+)'/", $content, $params)) {
                                // add the parameters discovered in the function file
                                $settings['params'] = implode(',', $params[1]);
                            } elseif (preg_match('/\w+hook$/', $func)) {
                                // default hook parameters
                                $settings['params'] = 'objectid,extrainfo';
                            } else {
                                $settings['params'] = '';
                            }
                            // flip from docache in config to nocache in settings
                            if (!empty($defaultmodulefunctions[$func])) {
                                $settings['nocache'] = 0;
                            } else {
                                $settings['nocache'] = 1;
                            }
                            $settings['usershared'] = 1;
                            $settings['cacheexpire'] = '';
                            $modulesettings[$name][$func] = $settings;
                        }
                    }
                }
                closedir($dh);
            }
        }

        $moduleconfig = [];
        foreach ($modules as $module) {
            // use the module name as key for easy lookup in xarModuleCache
            $name = $module['name'];
            // TODO: filter on something else ?
            if ($name == 'authsystem' ||
                $name == 'roles' ||
                $name == 'privileges') {
                continue;
            }
            $moduleconfig[$name] = $module;
            $moduleconfig[$name]['cachesettings'] = [];
            if (isset($modulesettings[$name])) {
                foreach ($modulesettings[$name] as $func => $settings) {
                    if ($settings['cacheexpire'] > 0) {
                        $settings['cacheexpire'] = CacheUtility::convertFromSeconds($settings['cacheexpire']);
                    }
                    $moduleconfig[$name]['cachesettings'][$func] = $settings;
                }
            }
            foreach ($modulefunctions[$name] as $func) {
                if (isset($moduleconfig[$name]['cachesettings'][$func])) {
                    continue;
                }
                $settings = [];
                if (preg_match('/\w+hook$/', $func)) {
                    // default hook parameters
                    $settings['params'] = 'objectid,extrainfo';
                } else {
                    $settings['params'] = '';
                }
                // flip from docache in config to nocache in settings
                if (!empty($defaultmodulefunctions[$func])) {
                    $settings['nocache'] = 0;
                } else {
                    $settings['nocache'] = 1;
                }
                $settings['usershared'] = 1;
                $settings['cacheexpire'] = '';
                $moduleconfig[$name]['cachesettings'][$func] = $settings;
            }
        }
        return $moduleconfig;
    }
}
