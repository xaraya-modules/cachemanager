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

use sys;

sys::import('modules.cachemanager.class.config');
sys::import('modules.cachemanager.class.utility');
use Xaraya\Modules\CacheManager\CacheConfig;
use Xaraya\Modules\CacheManager\CacheUtility;

class BlockCache extends CacheConfig
{
    public static function init(array $args = []) {}

    /**
     * configure block caching
     * @return array|void
     */
    public function modifyConfig($args)
    {
        extract($args);

        if (!$this->sec()->checkAccess('AdminXarCache')) {
            return;
        }

        $data = [];
        if (!$this->cache()->withOutput() || !$this->cache()->withBlocks()) {
            $data['blocks'] = [];
            return $data;
        }

        $this->var()->get('submit', $submit, 'str', '');
        if (!empty($submit)) {
            // Confirm authorisation code
            if (!$this->sec()->confirmAuthKey()) {
                return;
            }

            $this->var()->get('docache', $docache, 'isset', []);
            $this->var()->get('pageshared', $pageshared, 'isset', []);
            $this->var()->get('usershared', $usershared, 'isset', []);
            $this->var()->get('cacheexpire', $cacheexpire, 'isset', []);

            $newblocks = [];
            // loop over something that should return values for every block
            foreach ($cacheexpire as $bid => $expire) {
                $newblocks[$bid] = [];
                $newblocks[$bid]['bid'] = $bid;
                // flip from docache in template to nocache in settings
                if (!empty($docache[$bid])) {
                    $newblocks[$bid]['nocache'] = 0;
                } else {
                    $newblocks[$bid]['nocache'] = 1;
                }
                if (!empty($pageshared[$bid])) {
                    $newblocks[$bid]['pageshared'] = 1;
                } else {
                    $newblocks[$bid]['pageshared'] = 0;
                }
                if (!empty($usershared[$bid])) {
                    $newblocks[$bid]['usershared'] = intval($usershared[$bid]);
                } else {
                    $newblocks[$bid]['usershared'] = 0;
                }
                if (!empty($expire)) {
                    $expire = CacheUtility::convertToSeconds($expire);
                } elseif ($expire === '0') {
                    $expire = 0;
                } else {
                    $expire = null;
                }
                $newblocks[$bid]['cacheexpire'] = $expire;
            }
            $systemPrefix = $this->db()->getPrefix();
            $blocksettings = $systemPrefix . '_cache_blocks';
            $dbconn = $this->db()->getConn();

            // delete the whole cache blocks table and insert the new values
            $query = "DELETE FROM $blocksettings";
            $result = $dbconn->Execute($query);
            if (!$result) {
                return;
            }

            foreach ($newblocks as $block) {
                $query = "INSERT INTO $blocksettings (blockinstance_id,
                                                    nocache,
                                                    page,
                                                    theuser,
                                                    expire)
                            VALUES (?,?,?,?,?)";
                $bindvars = [$block['bid'], $block['nocache'], $block['pageshared'], $block['usershared'], $block['cacheexpire']];
                $result = $dbconn->Execute($query, $bindvars);
                if (!$result) {
                    return;
                }
            }

            // blocks could be anywhere, we're not smart enough not know exactly where yet
            $key = '';
            // so just flush all pages
            $this->cache()->flushPages($key);
            // and flush the blocks
            $this->cache()->flushBlocks($key);
            if ($this->mod()->getVar('AutoRegenSessionless')) {
                $this->mod()->apiFunc('cachemanager', 'admin', 'regenstatic');
            }
        }

        // Get all block caching configurations
        $data['blocks'] = $this->getConfig();

        $data['authid'] = $this->sec()->genAuthKey();
        return $data;
    }

    /**
     * get configuration of block caching for all blocks
     *
     * @return array|void Block caching configurations
     */
    public function getConfig()
    {
        $systemPrefix = $this->db()->getPrefix();
        $blocksettings = $systemPrefix . '_cache_blocks';
        $dbconn = $this->db()->getConn();
        $query = "SELECT blockinstance_id,
                nocache,
                page,
                theuser,
                expire
                FROM $blocksettings";
        $result = $dbconn->Execute($query);
        if (!$result) {
            return;
        }

        // Get all block instances
        $blocks = $this->mod()->apiFunc('blocks', 'user', 'getall');
        $bid2key = [];
        foreach ($blocks as $key => $block) {
            $bid2key[$block['bid']] = $key;
        }

        while ($result->next()) {
            [$bid, $nocache, $pageshared, $usershared, $cacheexpire] = $result->fields;
            if (!isset($bid2key[$bid])) {
                continue;
            }
            if (empty($nocache)) {
                $nocache = 0;
            }
            if (empty($pageshared)) {
                $pageshared = 0;
            }
            if (empty($usershared)) {
                $usershared = 0;
            }
            /*if (empty($cacheexpire)) {
                $cacheexpire = 0;
            }*/
            if ($cacheexpire > 0) {
                $cacheexpire = CacheUtility::convertFromSeconds($cacheexpire);
            }

            $key = $bid2key[$bid];
            $blocks[$key]['nocache'] = $nocache;
            $blocks[$key]['pageshared'] = $pageshared;
            $blocks[$key]['usershared'] = $usershared;
            $blocks[$key]['cacheexpire'] = $cacheexpire;
        }
        foreach ($blocks as $key => $block) {
            if (!isset($block['nocache'])) {
                // Try loading some defaults from the block init array (cfr. articles/random)
                if (!empty($block['module']) && !empty($block['type'])) {
                    $initresult = $this->mod()->apiFunc(
                        'blocks',
                        'user',
                        'read_type_init',
                        ['module' => $block['module'],
                            'type' => $block['type'], ]
                    );
                    if (!empty($initresult) && is_array($initresult)) {
                        if (isset($initresult['nocache'])) {
                            $block['nocache'] = $initresult['nocache'];
                            $blocks[$key]['nocache'] = $initresult['nocache'];
                        }
                        if (isset($initresult['pageshared'])) {
                            $block['pageshared'] = $initresult['pageshared'];
                            $blocks[$key]['pageshared'] = $initresult['pageshared'];
                        }
                        if (isset($initresult['usershared'])) {
                            $block['usershared'] = $initresult['usershared'];
                            $blocks[$key]['usershared'] = $initresult['usershared'];
                        }
                        if (isset($initresult['cacheexpire'])) {
                            $block['cacheexpire'] = $initresult['cacheexpire'];
                            $blocks[$key]['cacheexpire'] = $initresult['cacheexpire'];
                        }
                    }
                }
            }
            if (!isset($block['nocache'])) {
                $blocks[$key]['nocache'] = 0;
            }
            if (!isset($block['pageshared'])) {
                $blocks[$key]['pageshared'] = 0;
            }
            if (!isset($block['usershared'])) {
                $blocks[$key]['usershared'] = 0;
            }
            if (!isset($block['cacheexpire'])) {
                $blocks[$key]['cacheexpire'] = '';
            }
        }
        return $blocks;
    }
}
