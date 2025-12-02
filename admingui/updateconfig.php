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
use Xaraya\Modules\CacheManager\CacheManager;
use Xaraya\Modules\CacheManager\CacheUtility;
use Xaraya\Modules\MethodClass;
use sys;

/**
 * cachemanager admin updateconfig function
 * @extends MethodClass<AdminGui>
 */
class UpdateconfigMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Update the configuration parameters of the module based on data from the modification form
     * @return bool|void true on success of update
     * @see AdminGui::updateconfig()
     */
    public function __invoke(array $args = [])
    {
        // Get parameters
        $this->var()->find('cacheenabled', $cacheenabled, 'isset', 0);
        $this->var()->find('cachetheme', $cachetheme, 'str::24', '');
        $this->var()->find('cachesizelimit', $cachesizelimit, 'float:0.25:', 2);

        $this->var()->find('cachepages', $cachepages, 'isset', 0);
        $this->var()->find('pageexpiretime', $pageexpiretime, 'str:1:9', '00:30:00');
        $this->var()->find('pagedisplayview', $pagedisplayview, 'int:0:1', 0);
        $this->var()->find('pagetimestamp', $pagetimestamp, 'int:0:1', 0);
        $this->var()->find('expireheader', $expireheader, 'int:0:1', 0);
        $this->var()->find('pagehookedonly', $pagehookedonly, 'int:0:1', 0);
        $this->var()->find('autoregenerate', $autoregenerate, 'isset', 0);
        $this->var()->find('pagecachestorage', $pagecachestorage, 'str:1', 'filesystem');
        $this->var()->find('pagelogfile', $pagelogfile, 'str', '');
        $this->var()->find('pagesizelimit', $pagesizelimit, 'float:0.25:', 2);

        $this->var()->find('cacheblocks', $cacheblocks, 'isset', 0);
        $this->var()->find('blockexpiretime', $blockexpiretime, 'str:1:9', '0');
        $this->var()->find('blockcachestorage', $blockcachestorage, 'str:1', 'filesystem');
        $this->var()->find('blocklogfile', $blocklogfile, 'str', '');
        $this->var()->find('blocksizelimit', $blocksizelimit, 'float:0.25:', 2);

        $this->var()->find('cachemodules', $cachemodules, 'isset', 0);
        $this->var()->find('moduleexpiretime', $moduleexpiretime, 'str:1:9', '02:00:00');
        $this->var()->find('modulecachestorage', $modulecachestorage, 'str:1', 'filesystem');
        $this->var()->find('modulelogfile', $modulelogfile, 'str', '');
        $this->var()->find('modulesizelimit', $modulesizelimit, 'float:0.25:', 2);
        $this->var()->find('modulefunctions', $modulefunctions, 'isset', []);

        $this->var()->find('cacheobjects', $cacheobjects, 'isset', 0);
        $this->var()->find('objectexpiretime', $objectexpiretime, 'str:1:9', '02:00:00');
        $this->var()->find('objectcachestorage', $objectcachestorage, 'str:1', 'filesystem');
        $this->var()->find('objectlogfile', $objectlogfile, 'str', '');
        $this->var()->find('objectsizelimit', $objectsizelimit, 'float:0.25:', 2);
        $this->var()->find('objectmethods', $objectmethods, 'isset', []);

        // Confirm authorisation code
        if (!$this->sec()->confirmAuthKey()) {
            return;
        }
        // Security Check
        if (!$this->sec()->checkAccess('AdminXarCache')) {
            return;
        }

        // set the cache dir
        $varCacheDir = sys::varpath() . '/cache';
        $outputCacheDir = $varCacheDir . '/output';

        // turn output caching system on or off
        if (!empty($cacheenabled)) {
            if (!file_exists($outputCacheDir . '/cache.touch')) {
                touch($outputCacheDir . '/cache.touch');
            }
        } else {
            if (file_exists($outputCacheDir . '/cache.touch')) {
                unlink($outputCacheDir . '/cache.touch');
            }
        }

        // turn page level output caching on or off
        if (!empty($cachepages)) {
            if (!file_exists($outputCacheDir . '/cache.pagelevel')) {
                touch($outputCacheDir . '/cache.pagelevel');
            }
            if (!empty($pagelogfile) && !file_exists($pagelogfile)) {
                touch($pagelogfile);
            }
        } else {
            if (file_exists($outputCacheDir . '/cache.pagelevel')) {
                unlink($outputCacheDir . '/cache.pagelevel');
            }
            if (file_exists($outputCacheDir . '/autocache.start')) {
                unlink($outputCacheDir . '/autocache.start');
            }
            if (file_exists($outputCacheDir . '/autocache.log')) {
                unlink($outputCacheDir . '/autocache.log');
            }
        }

        // turn block level output caching on or off
        if ($cacheblocks) {
            if (!file_exists($outputCacheDir . '/cache.blocklevel')) {
                touch($outputCacheDir . '/cache.blocklevel');
            }
            if (!empty($blocklogfile) && !file_exists($blocklogfile)) {
                touch($blocklogfile);
            }
        } else {
            if (file_exists($outputCacheDir . '/cache.blocklevel')) {
                unlink($outputCacheDir . '/cache.blocklevel');
            }
        }

        // turn module level output caching on or off
        if ($cachemodules) {
            if (!file_exists($outputCacheDir . '/cache.modulelevel')) {
                touch($outputCacheDir . '/cache.modulelevel');
            }
            if (!empty($modulelogfile) && !file_exists($modulelogfile)) {
                touch($modulelogfile);
            }
        } else {
            if (file_exists($outputCacheDir . '/cache.modulelevel')) {
                unlink($outputCacheDir . '/cache.modulelevel');
            }
        }

        // turn object level output caching on or off
        if ($cacheobjects) {
            if (!file_exists($outputCacheDir . '/cache.objectlevel')) {
                touch($outputCacheDir . '/cache.objectlevel');
            }
            if (!empty($objectlogfile) && !file_exists($objectlogfile)) {
                touch($objectlogfile);
            }
        } else {
            if (file_exists($outputCacheDir . '/cache.objectlevel')) {
                unlink($outputCacheDir . '/cache.objectlevel');
            }
        }

        // convert size limit from MB to bytes
        $cachesizelimit = (intval($cachesizelimit * 1048576));
        $pagesizelimit = (intval($pagesizelimit * 1048576));
        $blocksizelimit = (intval($blocksizelimit * 1048576));
        $modulesizelimit = (intval($modulesizelimit * 1048576));
        $objectsizelimit = (intval($objectsizelimit * 1048576));

        //turn hh:mm:ss back into seconds
        $pageexpiretime = CacheUtility::convertToSeconds($pageexpiretime);
        $blockexpiretime = CacheUtility::convertToSeconds($blockexpiretime);
        $moduleexpiretime = CacheUtility::convertToSeconds($moduleexpiretime);
        $objectexpiretime = CacheUtility::convertToSeconds($objectexpiretime);

        // updated the config.caching settings
        $cachingConfigFile = $varCacheDir . '/config.caching.php';

        $configSettings = [];
        $configSettings['Output.DefaultTheme'] = $cachetheme;
        $configSettings['Output.SizeLimit'] = $cachesizelimit;
        $configSettings['Output.CookieName'] = $this->config()->getVar('Site.Session.CookieName');
        if (empty($configSettings['Output.CookieName'])) {
            $configSettings['Output.CookieName'] = 'XARAYASID';
        }
        $configSettings['Output.DefaultLocale'] = $this->mls()->getSiteLocale();
        $configSettings['Page.TimeExpiration'] = $pageexpiretime;
        $configSettings['Page.DisplayView'] = $pagedisplayview;
        $configSettings['Page.ShowTime'] = $pagetimestamp;
        $configSettings['Page.ExpireHeader'] = $expireheader;
        $configSettings['Page.HookedOnly'] = $pagehookedonly;
        $configSettings['Page.CacheStorage'] = $pagecachestorage;
        $configSettings['Page.LogFile'] = $pagelogfile;
        $configSettings['Page.SizeLimit'] = $pagesizelimit;

        $configSettings['Block.TimeExpiration'] = $blockexpiretime;
        $configSettings['Block.CacheStorage'] = $blockcachestorage;
        $configSettings['Block.LogFile'] = $blocklogfile;
        $configSettings['Block.SizeLimit'] = $blocksizelimit;

        $configSettings['Module.TimeExpiration'] = $moduleexpiretime;
        $configSettings['Module.CacheStorage'] = $modulecachestorage;
        $configSettings['Module.LogFile'] = $modulelogfile;
        $configSettings['Module.SizeLimit'] = $modulesizelimit;
        // update cache defaults for module functions
        $defaultmodulefunctions = unserialize((string) $this->mod()->getVar('DefaultModuleCacheFunctions'));
        foreach ($defaultmodulefunctions as $func => $docache) {
            if (!isset($modulefunctions[$func])) {
                $modulefunctions[$func] = 0;
            }
        }
        $configSettings['Module.CacheFunctions'] = $modulefunctions;
        $this->mod()->setVar('DefaultModuleCacheFunctions', serialize($modulefunctions));

        $configSettings['Object.TimeExpiration'] = $objectexpiretime;
        $configSettings['Object.CacheStorage'] = $objectcachestorage;
        $configSettings['Object.LogFile'] = $objectlogfile;
        $configSettings['Object.SizeLimit'] = $objectsizelimit;
        // update cache defaults for object methods
        $defaultobjectmethods = unserialize((string) $this->mod()->getVar('DefaultObjectCacheMethods'));
        foreach ($defaultobjectmethods as $method => $docache) {
            if (!isset($objectmethods[$method])) {
                $objectmethods[$method] = 0;
            }
        }
        $configSettings['Object.CacheMethods'] = $objectmethods;
        $this->mod()->setVar('DefaultObjectCacheMethods', serialize($objectmethods));

        CacheManager::save_config(
            ['configSettings' => $configSettings,
                'cachingConfigFile' => $cachingConfigFile, ]
        );

        // see if we need to flush the cache when a new comment is added for some item
        $this->var()->find('pageflushcomment', $pageflushcomment, 'isset', 0);
        if ($pageflushcomment && $pagedisplayview) {
            $this->mod()->setVar('FlushOnNewComment', 1);
        } else {
            $this->mod()->setVar('FlushOnNewComment', 0);
        }

        // see if we need to flush the cache when a new rating is added for some item
        $this->var()->find('pageflushrating', $pageflushrating, 'isset', 0);
        if ($pageflushrating  && $pagedisplayview) {
            $this->mod()->setVar('FlushOnNewRating', 1);
        } else {
            $this->mod()->setVar('FlushOnNewRating', 0);
        }

        // see if we need to flush the cache when a new vote is cast on a poll hooked to some item
        $this->var()->find('pageflushpollvote', $pageflushpollvote, 'isset', 0);
        if ($pageflushpollvote && $pagedisplayview) {
            $this->mod()->setVar('FlushOnNewPollvote', 1);
        } else {
            $this->mod()->setVar('FlushOnNewPollvote', 0);
        }

        // set option for auto regeneration of session-less url list cache on event invalidation
        if ($autoregenerate) {
            $this->mod()->setVar('AutoRegenSessionless', 1);
        } else {
            $this->mod()->setVar('AutoRegenSessionless', 0);
        }

        // flush adminpanels and base blocks to show new menu options if necessary
        if ($cacheblocks) {
            // get the output cache directory so you can flush items even if output caching is disabled
            $outputCacheDir = $this->cache()->getOutputCacheDir();

            // get the cache storage for block caching
            $cachestorage = $this->cache()->getStorage(['storage'  => $blockcachestorage,
                'type'     => 'block',
                'cachedir' => $outputCacheDir, ]);
            if (!empty($cachestorage)) {
                $cachestorage->flushCached('base-');
                // CHECKME: no longer used ?
                $cachestorage->flushCached('adminpanels-');
            }
        }

        $this->ctl()->redirect($this->mod()->getURL('admin', 'modifyconfig'));

        return true;
    }
}
