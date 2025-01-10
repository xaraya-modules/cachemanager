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

use Xaraya\Modules\CacheManager\CacheManager;
use Xaraya\Modules\CacheManager\StatsApi;
use Xaraya\Modules\MethodClass;
use xarSecurity;
use xarVar;
use xarCache;
use xarModVars;
use xarMod;
use xarSec;
use xarController;
use xarTplPager;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * cachemanager admin stats function
 * @extends MethodClass<\Xaraya\Modules\CacheManager\AdminGui>
 */
class StatsMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Show cache statistics
     * @param array $args with optional arguments:
     * - string $args['tab']
     * - int    $args['withlog']
     * - string $args['reset']
     * - string $args['sort']
     * - int    $args['startnum']
     * - int    $args['itemsperpage']
     * @return array|bool|void
     */
    public function __invoke(array $args = [])
    {
        if (!xarSecurity::check('AdminXarCache')) {
            return;
        }

        extract($args);
        if (!xarVar::fetch('tab', 'str', $tab, 'overview', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('sort', 'str', $sort, '', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('reset', 'str', $reset, '', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('startnum', 'int', $startnum, 1, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('withlog', 'int', $withlog, 0, xarVar::NOT_REQUIRED)) {
            return;
        }

        // Get the output cache directory so you can view stats even if output caching is disabled
        $outputCacheDir = xarCache::getOutputCacheDir();

        $numitems = xarModVars::get('cachemanager', 'itemsperpage');
        if (empty($numitems)) {
            $numitems = 100;
            xarModVars::set('cachemanager', 'itemsperpage', $numitems);
        }

        $data = [];

        // get cache status
        $data['status'] = xarMod::apiFunc('cachemanager', 'admin', 'getstatus');

        $data['tab'] = $tab;
        $data['itemsperpage'] = $numitems;

        // get the caching config settings from the config file
        $data['settings'] = CacheManager::get_config(
            ['from' => 'file', 'tpl_prep' => true]
        );

        // get StatsApi component from AdminGui parent here
        $statsApi = $this->getParent()->getStatsAPI();
        assert($statsApi instanceof StatsApi);

        switch ($tab) {
            case 'page':
            case 'block':
            case 'module':
            case 'object':
            case 'variable':
                $upper = ucfirst($tab);
                $enabled   = $upper . 'CachingEnabled'; // e.g. PageCachingEnabled
                $storage   = $upper . 'CacheStorage'; // e.g. BlockCacheStorage
                $provider  = $upper . 'CacheProvider'; // e.g. VariableCacheProvider
                $logfile   = $upper . 'LogFile'; // e.g. ModuleLogFile
                $cachetime = $upper . 'TimeExpiration'; // e.g. ObjectTimeExpiration
                $sizelimit = $upper . 'SizeLimit'; // e.g. VariableSizeLimit

                if (!empty($reset)) {
                    // Confirm authorisation code
                    if (!xarSec::confirmAuthKey()) {
                        return;
                    }

                    if (!empty($data['settings'][$logfile]) && file_exists($data['settings'][$logfile])) {
                        $fh = fopen($data['settings'][$logfile], 'w');
                        if (!empty($fh)) {
                            fclose($fh);
                        }
                    }

                    xarController::redirect(xarController::URL(
                        'cachemanager',
                        'admin',
                        'stats',
                        ['tab' => $tab]
                    ), null, $this->getContext());
                    return true;
                }
                if (!empty($data['status'][$enabled]) && !empty($data['settings'][$storage])) {
                    if (empty($data['settings'][$provider])) {
                        $data['settings'][$provider] = null;
                    }
                    // get cache storage
                    $cachestorage = xarCache::getStorage(['storage'   => $data['settings'][$storage],
                        'type'      => $tab,
                        'provider'  => $data['settings'][$provider],
                        'cachedir'  => $outputCacheDir,
                        'expire'    => $data['settings'][$cachetime],
                        'sizelimit' => $data['settings'][$sizelimit], ]);
                    // clean the cache first
                    if (!empty($data['settings'][$cachetime])) {
                        $cachestorage->cleanCached();
                    }
                    $data['cacheinfo'] = $cachestorage->getCacheInfo();
                    $data['cacheinfo']['total'] = $data['cacheinfo']['hits'] + $data['cacheinfo']['misses'];
                    if (!empty($data['cacheinfo']['total'])) {
                        $data['cacheinfo']['ratio'] = sprintf("%.1f", 100.0 * $data['cacheinfo']['hits'] / $data['cacheinfo']['total']);
                    } else {
                        $data['cacheinfo']['ratio'] = 0.0;
                    }
                    if (!empty($data['cacheinfo']['size'])) {
                        $data['cacheinfo']['size'] = round($data['cacheinfo']['size'] / 1048576, 2);
                    }
                    $data['cacheinfo']['storage'] = $data['settings'][$storage];
                    // get a list of items in cache
                    $data['items'] = $cachestorage->getCachedList();
                    // get a list of keys in cache
                    $cachekeys = [];
                    foreach ($data['items'] as $item) {
                        $cachekeys[$item['key']] = 1;
                    }
                    $data['cachekeys'] = array_keys($cachekeys);
                    unset($cachekeys);
                    // Generate a one-time authorisation code for this operation
                    $data['authid'] = xarSec::genAuthKey();
                    // analyze logfile
                    if (!empty($withlog) && !empty($data['settings'][$logfile]) && file_exists($data['settings'][$logfile]) && filesize($data['settings'][$logfile]) > 0) {
                        $data['withlog'] = 1;
                        $data['totals'] = [];
                        $statsApi->logfile($data['items'], $data['totals'], $data['settings'][$logfile], $tab);
                        if (!empty($data['totals']['size'])) {
                            $data['totals']['size'] = round($data['totals']['size'] / 1048576, 2);
                        }
                        $data['totals']['file'] = $data['settings'][$logfile];
                    } else {
                        $data['withlog'] = null;
                        $data['loginfo'] = [];
                        // status field = 1
                        $statsApi->filestats($data['loginfo'], $data['settings'][$logfile], 1, 1);
                        if (!empty($data['loginfo']['size'])) {
                            $data['loginfo']['size'] = round($data['loginfo']['size'] / 1048576, 2);
                        }
                        $data['loginfo']['file'] = $data['settings'][$logfile];
                    }
                    // sort items
                    if (empty($sort) || $sort == 'id') {
                        $sort = null;
                        ksort($data['items']);
                    } else {
                        $statsApi->sortitems($data['items'], $sort);
                    }
                    // get pager
                    $count = count($data['items']);
                    if ($count > $numitems) {
                        $keys = array_slice(array_keys($data['items']), $startnum - 1, $numitems);
                        $items = [];
                        foreach ($keys as $key) {
                            $items[$key] = $data['items'][$key];
                        }
                        $data['items'] = $items;
                        unset($keys);
                        unset($items);
                        sys::import('xaraya.pager');
                        $data['pager'] = xarTplPager::getPager(
                            $startnum,
                            $count,
                            xarController::URL(
                                'cachemanager',
                                'admin',
                                'stats',
                                ['tab' => $tab,
                                    'withlog' => empty($data['withlog']) ? null : 1,
                                    'sort' => $sort,
                                    'startnum' => '%%', ]
                            ),
                            $numitems
                        );
                    }
                } else {
                    $data['items'] = [];
                    $data['withlog'] = null;
                }
                break;

            case 'query':
                // TODO: Get some query cache statistics when available
                break;

            case 'autocache':
                if (!empty($reset)) {
                    // Confirm authorisation code
                    if (!xarSec::confirmAuthKey()) {
                        return;
                    }

                    if (!empty($withlog)) {
                        if (file_exists($outputCacheDir . '/autocache.log')) {
                            $fh = fopen($outputCacheDir . '/autocache.log', 'w');
                            if (!empty($fh)) {
                                fclose($fh);
                            }
                        }
                    } elseif (file_exists($outputCacheDir . '/autocache.stats')) {
                        $fh = fopen($outputCacheDir . '/autocache.stats', 'w');
                        if (!empty($fh)) {
                            fclose($fh);
                        }
                    }

                    xarController::redirect(xarController::URL(
                        'cachemanager',
                        'admin',
                        'stats',
                        ['tab' => 'autocache']
                    ), null, $this->getContext());
                    return true;
                }

                // Get some statistics from the auto-cache stats file
                $data['items'] = [];
                $data['totals'] = ['hit' => 0,
                    'miss' => 0,
                    'total' => 0,
                    'ratio' => 0,
                    'first' => 0,
                    'last' => 0, ];
                if (file_exists($outputCacheDir . '/autocache.stats') &&
                    filesize($outputCacheDir . '/autocache.stats') > 0) {
                    // analyze statsfile
                    $statsApi->autostats($data['items'], $data['totals'], $outputCacheDir . '/autocache.stats');
                }
                if (!empty($withlog) && file_exists($outputCacheDir . '/autocache.log') &&
                    filesize($outputCacheDir . '/autocache.log') > 0) {
                    $data['withlog'] = 1;
                    // analyze logfile and merge with stats items
                    $statsApi->autolog($data['items'], $data['totals'], $outputCacheDir . '/autocache.log');
                }
                if (count($data['items']) > 0) {
                    // sort items
                    if (empty($sort) || $sort == 'page') {
                        $sort = null;
                        ksort($data['items']);
                    } else {
                        $statsApi->sortitems($data['items'], $sort);
                    }
                    // get pager
                    $count = count($data['items']);
                    if ($count > $numitems) {
                        $keys = array_slice(array_keys($data['items']), $startnum - 1, $numitems);
                        $items = [];
                        foreach ($keys as $key) {
                            $items[$key] = $data['items'][$key];
                        }
                        $data['items'] = $items;
                        unset($keys);
                        unset($items);
                        $data['pager'] = xarTplPager::getPager(
                            $startnum,
                            $count,
                            xarController::URL(
                                'cachemanager',
                                'admin',
                                'stats',
                                ['tab' => 'autocache',
                                    'sort' => $sort,
                                    'startnum' => '%%', ]
                            ),
                            $numitems
                        );
                    }
                }
                break;

            case 'overview':
            default:
                // set items per page
                if (!xarVar::fetch('itemsperpage', 'int', $itemsperpage, 0, xarVar::NOT_REQUIRED)) {
                    return;
                }
                if (!empty($itemsperpage)) {
                    xarModVars::set('cachemanager', 'itemsperpage', $itemsperpage);
                    $data['itemsperpage'] = $itemsperpage;
                }
                // list of cache types to check
                $typelist = ['page', 'block', 'module', 'object', 'variable'];
                foreach ($typelist as $type) {
                    $upper = ucfirst($type);
                    $enabled   = $upper . 'CachingEnabled'; // e.g. PageCachingEnabled
                    $storage   = $upper . 'CacheStorage'; // e.g. BlockCacheStorage
                    $provider  = $upper . 'CacheProvider'; // e.g. VariableCacheProvider
                    $logfile   = $upper . 'LogFile'; // e.g. ModLogFile
                    $cachetime = $upper . 'TimeExpiration'; // e.g. ObjectTimeExpiration
                    $sizelimit = $upper . 'SizeLimit'; // e.g. VariableSizeLimit
                    $cachevar  = $type . 'cache'; // e.g. pagecache
                    $logvar    = $type . 'log'; // e.g. blocklog

                    // get cache stats
                    $data[$cachevar] = ['size'    => 0,
                        'items'   => 0,
                        'hits'    => 0,
                        'misses'  => 0,
                        'modtime' => 0, ];
                    if ($data['status'][$enabled] && !empty($data['settings'][$storage])) {
                        if (empty($data['settings'][$provider])) {
                            $data['settings'][$provider] = null;
                        }
                        $cachestorage = xarCache::getStorage(['storage'   => $data['settings'][$storage],
                            'type'      => $type,
                            'provider'  => $data['settings'][$provider],
                            'cachedir'  => $outputCacheDir,
                            'expire'    => $data['settings'][$cachetime],
                            'sizelimit' => $data['settings'][$sizelimit], ]);
                        // clean the cache first
                        if (!empty($data['settings'][$cachetime])) {
                            $cachestorage->cleanCached();
                        }
                        $data[$cachevar] = $cachestorage->getCacheInfo();
                        if (!empty($data[$cachevar]['size'])) {
                            $data[$cachevar]['size'] = round($data[$cachevar]['size'] / 1048576, 2);
                        }
                    }
                    $data[$cachevar]['total'] = $data[$cachevar]['hits'] + $data[$cachevar]['misses'];
                    if (!empty($data[$cachevar]['total'])) {
                        $data[$cachevar]['ratio'] = sprintf("%.1f", 100.0 * $data[$cachevar]['hits'] / $data[$cachevar]['total']);
                    } else {
                        $data[$cachevar]['ratio'] = 0.0;
                    }
                    // get logfile stats
                    if ($data['status'][$enabled] && !empty($data['settings'][$logfile])) {
                        $data[$logvar] = [];
                        // status field = 1
                        $statsApi->filestats($data[$logvar], $data['settings'][$logfile], 1, 1);
                        if (!empty($data[$logvar]['size'])) {
                            $data[$logvar]['size'] = round($data[$logvar]['size'] / 1048576, 2);
                        }
                    }
                }

                // Note: the query cache is actually handled by ADODB
                // get query cache stats
                $data['settings']['QueryCacheStorage'] = 'filesystem';
                $data['querycache'] = ['size'  => 0,
                    'items' => 0, ];
                if ($data['status']['QueryCachingEnabled'] && !empty($data['settings']['QueryCacheStorage'])) {
                    $querystorage = xarCache::getStorage(['storage'  => $data['settings']['QueryCacheStorage'],
                        'type'     => 'database',
                        'cachedir' => sys::varpath() . '/cache', ]);
                    $data['querycache']['size'] = $querystorage->getCacheSize(true);
                    $data['querycache']['items'] = $querystorage->getCacheItems() - 1; // index.html
                }

                // get auto-cache stats
                $data['settings']['AutoCacheLogFile'] = $outputCacheDir . '/autocache.log';
                if ($data['status']['AutoCachingEnabled'] && !empty($data['settings']['AutoCacheLogFile'])) {
                    $data['autocachelog'] = [];
                    // status field = 1
                    $statsApi->filestats($data['autocachelog'], $data['settings']['AutoCacheLogFile'], 1, 1);
                    if (!empty($data['autocachelog']['size'])) {
                        $data['autocachelog']['size'] = round($data['autocachelog']['size'] / 1048576, 2);
                    }
                }
                if ($data['status']['AutoCachingEnabled'] && file_exists($outputCacheDir . '/autocache.stats')) {
                    $data['settings']['AutoCacheStatFile'] = $outputCacheDir . '/autocache.stats';
                } else {
                    $data['settings']['AutoCacheStatFile'] = '';
                }
                if ($data['status']['AutoCachingEnabled'] && !empty($data['settings']['AutoCacheStatFile'])) {
                    $data['autocachestat'] = [];
                    // hit field = 1, miss field = 2
                    $statsApi->filestats($data['autocachestat'], $data['settings']['AutoCacheStatFile'], 1, 2);
                    if (!empty($data['autocachestat']['size'])) {
                        $data['autocachestat']['size'] = round($data['autocachestat']['size'] / 1048576, 2);
                    }
                }
                break;
        }

        return $data;
    }
}
