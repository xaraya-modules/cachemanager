<?php

/**
 * @package modules\cachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\CacheManager;

use Xaraya\Modules\AdminApiClass;
use xarVar;
use sys;

sys::import('xaraya.modules.adminapi');

/**
 * Handle the cachemanager stats API
 * @extends AdminApiClass<Module>
 */
class StatsApi extends AdminApiClass
{
    public function configure()
    {
        $this->setModType('stats');
        // don't call xarMod:apiLoad() for cachemanager stats API
    }

    /**
     * count the total number of lines, hits and misses in a logfile
     */
    public function filestats(&$totals, $logfile, $hitfield = null, $missfield = null)
    {
        $totals = ['size'  => 0,
            'lines' => 0,
            'hit'   => 0,
            'miss'  => 0,
            'total' => 0,
            'ratio' => 0, ];
        if (empty($logfile) || !file_exists($logfile) || filesize($logfile) < 1) {
            return;
        }

        $totals['size'] = filesize($logfile);

        $fp = fopen($logfile, 'r');
        if (empty($fp)) {
            return;
        }

        while (!feof($fp)) {
            $entry = fgets($fp, 1024);
            $entry = trim($entry);
            if (empty($entry)) {
                continue;
            }
            $totals['lines']++;
            if (!isset($hitfield) || !isset($missfield)) {
                continue;
            }
            $fields = explode(' ', $entry);
            // we're dealing with a status field in a logfile
            if ($hitfield == $missfield) {
                if (!isset($fields[$hitfield])) {
                    continue;
                }
                $status = strtolower($fields[$hitfield]);
                $totals[$status]++;
                // we're dealing with separate fields in a stats file
            } else {
                if (!isset($fields[$hitfield]) || !isset($fields[$missfield])) {
                    continue;
                }
                $totals['hit'] += $fields[$hitfield];
                $totals['miss'] += $fields[$missfield];
            }
        }
        fclose($fp);
        $totals['total'] = $totals['hit'] + $totals['miss'];
        if (!empty($totals['total'])) {
            $totals['ratio'] = sprintf("%.1f", 100.0 * $totals['hit'] / $totals['total']);
        } else {
            $totals['ratio'] = 0.0;
        }
    }

    /**
     * analyze cache storage logfile for hits and misses and merge with items list
     */
    public function logfile(&$items, &$totals, $logfile, $checktype)
    {
        if (empty($logfile) || !file_exists($logfile) || filesize($logfile) < 1) {
            return;
        }

        $stats = [];
        $pages = [];
        $fh = fopen($logfile, 'r');
        if (empty($fh)) {
            return;
        }

        while (!feof($fh)) {
            $entry = fgets($fh, 1024);
            $entry = trim($entry);
            if (empty($entry)) {
                continue;
            }
            [$time, $status, $type, $key, $code, $addr, $url] = explode(' ', $entry);
            if ($type != $checktype) {
                continue;
            }
            $status = strtolower($status);
            if (!isset($stats[$key])) {
                $stats[$key] = [];
            }
            if (!isset($stats[$key][$code])) {
                $stats[$key][$code] = ['hit'   => 0,
                    'miss'  => 0,
                    'first' => $time,
                    'last'  => 0,
                    'pages' => [], ];
            }
            $stats[$key][$code][$status]++;
            $stats[$key][$code]['last'] = $time;
            if (!isset($stats[$key][$code]['pages'][$url])) {
                $stats[$key][$code]['pages'][$url] = 0;
            }
            $stats[$key][$code]['pages'][$url]++;
            if (!isset($pages[$url])) {
                $pages[$url] = 0;
            }
            $pages[$url]++;
        }
        $totals = ['hit'   => 0,
            'miss'  => 0,
            'total' => 0,
            'ratio' => 0,
            'first' => 0,
            'last'  => 0,
            'size'  => filesize($logfile),
            'pages' => count($pages), ];
        unset($pages);

        $keycode2id = [];
        foreach (array_keys($items) as $id) {
            $keycode = $items[$id]['key'] . '-' . $items[$id]['code'];
            $keycode2id[$keycode] = $id;
        }
        // calculate totals and ratios
        foreach (array_keys($stats) as $key) {
            foreach (array_keys($stats[$key]) as $code) {
                $keycode = $key . '-' . $code;
                if (isset($keycode2id[$keycode])) {
                    $id = $keycode2id[$keycode];
                    $items[$id]['hit'] = $stats[$key][$code]['hit'];
                    $items[$id]['miss'] = $stats[$key][$code]['miss'];
                    $items[$id]['total'] = $stats[$key][$code]['hit'] + $stats[$key][$code]['miss'];
                    if (!empty($items[$id]['total'])) {
                        $items[$id]['ratio'] = sprintf("%.1f", 100.0 * $items[$id]['hit'] / $items[$id]['total']);
                    } else {
                        $items[$id]['ratio'] = 0.0;
                    }
                    $items[$id]['first'] = $stats[$key][$code]['first'];
                    $items[$id]['last'] = $stats[$key][$code]['last'];
                    $items[$id]['pages'] = count($stats[$key][$code]['pages']);
                } else {
                    $item = ['key'   => $key,
                        'code'  => $code,
                        'time'  => 0,
                        'size'  => -1,
                        'check' => '', ];
                    $item['hit'] = $stats[$key][$code]['hit'];
                    $item['miss'] = $stats[$key][$code]['miss'];
                    $item['total'] = $stats[$key][$code]['hit'] + $stats[$key][$code]['miss'];
                    if (!empty($item['total'])) {
                        $item['ratio'] = sprintf("%.1f", 100.0 * $item['hit'] / $item['total']);
                    } else {
                        $item['ratio'] = 0.0;
                    }
                    $item['first'] = $stats[$key][$code]['first'];
                    $item['last'] = $stats[$key][$code]['last'];
                    $item['pages'] = count($stats[$key][$code]['pages']);
                    $items[] = $item;
                }
                $totals['hit'] += $stats[$key][$code]['hit'];
                $totals['miss'] += $stats[$key][$code]['miss'];
                if (empty($totals['first']) ||
                    $totals['first'] > $stats[$key][$code]['first']) {
                    $totals['first'] = $stats[$key][$code]['first'];
                }
                if (empty($totals['last']) ||
                    $totals['last'] < $stats[$key][$code]['last']) {
                    $totals['last'] = $stats[$key][$code]['last'];
                }
            }
        }
        $totals['total'] = $totals['hit'] + $totals['miss'];
        if (!empty($totals['total'])) {
            $totals['ratio'] = sprintf("%.1f", 100.0 * $totals['hit'] / $totals['total']);
        } else {
            $totals['ratio'] = 0.0;
        }
        unset($keycode2id);
        unset($stats);
        foreach (array_keys($items) as $id) {
            if (!isset($items[$id]['hit'])) {
                $items[$id]['hit'] = '';
                $items[$id]['miss'] = '';
                $items[$id]['total'] = '';
                $items[$id]['ratio'] = '';
                $items[$id]['first'] = '';
                $items[$id]['last'] = '';
                $items[$id]['pages'] = '';
            }
        }
    }

    /**
     * analyze auto-cache statsfile for hits and misses
     */
    public function autostats(&$items, &$totals, $logfile)
    {
        if (empty($logfile) || !file_exists($logfile) || filesize($logfile) < 1) {
            return;
        }

        $fh = fopen($logfile, 'r');
        if (empty($fh)) {
            return;
        }

        while (!feof($fh)) {
            $entry = fgets($fh, 1024);
            $entry = trim($entry);
            if (empty($entry)) {
                continue;
            }
            [$url, $hit, $miss, $first, $last] = explode(' ', $entry);
            $page = $url;
            if (strlen($page) > 105) {
                $page = wordwrap($page, 105, "\n", 1);
            }
            $hit = intval($hit);
            $miss = intval($miss);
            $page = xarVar::prepForDisplay($page);
            $items[$url] = ['page' => $page,
                'hit' => $hit,
                'miss' => $miss,
                'total' => ($hit + $miss),
                'ratio' => sprintf("%.1f", 100.0 * $hit / ($hit + $miss)),
                'first' => $first,
                'last' => $last, ];
            $totals['hit'] += $hit;
            $totals['miss'] += $miss;
            if (empty($totals['first']) ||
                $totals['first'] > $first) {
                $totals['first'] = $first;
            }
            if (empty($totals['last']) ||
                $totals['last'] < $last) {
                $totals['last'] = $last;
            }
        }
        fclose($fh);
        $totals['total'] = $totals['hit'] + $totals['miss'];
        if (!empty($totals['total'])) {
            $totals['ratio'] = sprintf("%.1f", 100.0 * $totals['hit'] / $totals['total']);
        } else {
            $totals['ratio'] = 0.0;
        }
    }

    /**
     * analyze auto-cache logfile for hits and misses and merge with stats items
     */
    public function autolog(&$items, &$totals, $logfile)
    {
        if (empty($logfile) || !file_exists($logfile) || filesize($logfile) < 1) {
            return;
        }

        $fh = fopen($logfile, 'r');
        if (empty($fh)) {
            return;
        }

        while (!feof($fh)) {
            $entry = fgets($fh, 1024);
            $entry = trim($entry);
            if (empty($entry)) {
                continue;
            }
            [$time, $status, $addr, $url] = explode(' ', $entry);
            $status = strtolower($status);
            if (!isset($items[$url])) {
                $items[$url] =  ['hit'   => 0,
                    'miss'  => 0,
                    'first' => $time,
                    'last'  => 0, ];
            }
            $items[$url][$status]++;
            if (empty($items[$url]['first']) ||
                $items[$url]['first'] > $time) {
                $items[$url]['first'] = $time;
            }
            if (empty($items[$url]['last']) ||
                $items[$url]['last'] < $time) {
                $items[$url]['last'] = $time;
            }
        }
        fclose($fh);
        $totals = ['hit'   => 0,
            'miss'  => 0,
            'total' => 0,
            'ratio' => 0,
            'first' => 0,
            'last'  => 0, ];

        // re-calculate totals and ratios
        foreach (array_keys($items) as $url) {
            $page = $url;
            if (strlen($page) > 105) {
                $page = wordwrap($page, 105, "\n", 1);
            }
            $items[$url]['page'] = xarVar::prepForDisplay($page);
            $items[$url]['total'] = $items[$url]['hit'] + $items[$url]['miss'];
            if (!empty($items[$url]['total'])) {
                $items[$url]['ratio'] = sprintf("%.1f", 100.0 * $items[$url]['hit'] / $items[$url]['total']);
            } else {
                $items[$url]['ratio'] = 0.0;
            }
            $totals['hit'] += $items[$url]['hit'];
            $totals['miss'] += $items[$url]['miss'];
            if (empty($totals['first']) ||
                $totals['first'] > $items[$url]['first']) {
                $totals['first'] = $items[$url]['first'];
            }
            if (empty($totals['last']) ||
                $totals['last'] < $items[$url]['last']) {
                $totals['last'] = $items[$url]['last'];
            }
        }
        $totals['total'] = $totals['hit'] + $totals['miss'];
        if (!empty($totals['total'])) {
            $totals['ratio'] = sprintf("%.1f", 100.0 * $totals['hit'] / $totals['total']);
        } else {
            $totals['ratio'] = 0.0;
        }
    }

    /**
     * sort items
     */
    public function sortitems(&$items, $sort)
    {
        $sort = strtolower($sort);

        switch ($sort) {
            case 'key':
            case 'code':
                uasort($items, function ($a, $b) use ($sort) {
                    return strcmp($a[$sort], $b[$sort]);
                });
                break;

            case 'time':
            case 'size':
            case 'hit':
            case 'miss':
            case 'total':
            case 'ratio':
            case 'first':
            case 'last':
            case 'pages':
                uasort($items, function ($a, $b) use ($sort) {
                    if ($a[$sort] == $b[$sort]) {
                        return 0;
                    }
                    return ($a[$sort] > $b[$sort]) ? -1 : 1;
                });
                break;

            default:
                return;
        }
    }
}
