<?php

/**
 * Pre-fetch pages for caching (executed by the scheduler module)
 *
 * @package modules\cachemanager
 * @subpackage cachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.6.2
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/182.html
 *
 * @author mikespub <mikespub@xaraya.com>
**/

namespace Xaraya\Modules\CacheManager;

use xarObject;
use sys;

sys::import('xaraya.services.xar');
use Xaraya\Services\xar;

class CacheScheduler extends xarObject
{
    public static function init(array $args = []) {}

    /**
     * regenerate the page output cache of URLs in the session-less list
     * @author jsb
     *
     * @return string
     */
    public static function regenstatic($nolimit = null)
    {
        $method = __METHOD__;
        $logs = [];
        $logs[] = "$method start";
        $urls = [];
        $outputCacheDir = sys::varpath() . '/cache/output/';

        $xar = xar::getServicesClass();
        // make sure output caching is really enabled, and that we are caching pages
        if (!$xar->cache()->withOutput() || !$xar->cache()->withPages()) {
            $logs[] = "$method no page caching";
            $logs[] = "$method stop";
            return implode("\n", $logs);
        }

        // flush the static pages
        $xar->cache()->flushPages('static');

        $configKeys = ['Page.SessionLess'];
        $sessionlessurls = CacheManager::get_config(
            ['keys' => $configKeys, 'from' => 'file', 'viahook' => true]
        );

        $urls = $sessionlessurls['Page.SessionLess'];

        if (!$nolimit) {
            // randomize the order of the urls just in case the timelimit cuts the
            // process short - no need to always drop the same pages.
            shuffle($urls);

            // set a time limit for the regeneration
            // TODO: make the timelimit variable and configurable.
            $timelimit = time() + 10;
        }

        foreach ($urls as $url) {
            $logs[] = "$method get $url";
            // Make sure the url isn't empty before calling getfile()
            if (strlen(trim($url))) {
                $xar->mod()->apiFunc('base', 'user', 'getfile', ['url' => $url, 'superrors' => true]);
            }
            if (!$nolimit && time() > $timelimit) {
                break;
            }
        }
        $logs[] = "$method stop";

        return implode("\n", $logs);
    }

    /**
     * This is a poor-man's alternative for using wget in a cron job :
     * wget -r -l 1 -w 2 -nd --delete-after -o /tmp/wget.log http://www.mysite.com/
     *
     * @author mikespub
     * @access private
     */
    public static function prefetch($args)
    {
        extract($args);
        $method = __METHOD__;
        $logs = [];
        $logs[] = "$method start";

        // default start page is the homepage
        if (empty($starturl)) {
            $starturl = $xar->ctl()->getBaseURL();
        }
        // default is go 1 level deep
        if (!isset($maxlevel)) {
            $maxlevel = 1;
        }
        // default is wait 2 seconds
        if (!isset($wait)) {
            $wait = 2;
        }
        $xar = xar::getServicesClass();
        // avoid the current page just in case...
        $avoid = $xar->ctl()->getCurrentURL([], false);

        $level = 0;
        $seen = [];
        $todo = [$starturl];

        // breadth-first
        while ($level <= $maxlevel && count($todo) > 0) {
            $found = [];
            foreach ($todo as $url) {
                $seen[$url] = 1;

                $logs[] = "$method get $url";
                // get the current page
                $page = $xar->mod()->apiFunc(
                    'base',
                    'user',
                    'getfile',
                    ['url' => $url]
                );
                if (empty($page)) {
                    continue;
                }

                // extract local links only (= default)
                $links = $xar->mod()->apiFunc(
                    'base',
                    'user',
                    'extractlinks',
                    ['content' => $page]
                );
                foreach ($links as $link) {
                    $found[$link] = 1;
                }

                // wait a while before retrieving the next page
                if (!empty($wait)) {
                    sleep($wait);
                }
            }
            $todo = [];
            foreach (array_keys($found) as $link) {
                if (!isset($seen[$link]) && $link != $avoid) {
                    $todo[] = $link;
                }
            }
            $level++;
        }
        $logs[] = "$method stop";

        return implode("\n", $logs);
    }
}
