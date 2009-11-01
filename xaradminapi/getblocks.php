<?php
/**
 * Get configuration of block caching for blocks
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
 * get configuration of block caching for all blocks
 *
 * @return array Block caching configurations
 */
function xarcachemanager_adminapi_getblocks($args)
{
    extract($args);

    $systemPrefix = xarDB::getPrefix();
    $blocksettings = $systemPrefix . '_cache_blocks';
    $dbconn = xarDB::getConn();
    $query = "SELECT blockinstance_id,
             nocache,
             page,
             theuser,
             expire
             FROM $blocksettings";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get all block instances
    $blocks = xarModAPIfunc('blocks', 'user', 'getall');
    $bid2key = array();
    foreach ($blocks as $key => $block) {
        $bid2key[$block['bid']] = $key;
    }

    while (!$result->EOF) {
        list ($bid, $nocache, $pageshared, $usershared, $cacheexpire) = $result->fields;
        $result->MoveNext();
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
        if ($cacheexpire > 0 ) {
            $cacheexpire = xarMod::apiFunc( 'xarcachemanager', 'admin', 'convertseconds',
                                          array('starttime' => $cacheexpire,
                                                'direction' => 'from'));
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
                $initresult = xarModAPIfunc('blocks', 'user', 'read_type_init',
                                            array('module' => $block['module'],
                                                  'type' => $block['type']));
                if (!empty($initresult) && is_array($initresult)) {
                    if (isset($initresult['nocache'])) {
                        $block['nocache'] = $initresult['nocache'];
                        $blocks[$key]['nocache'] = $initresult['nocache'];
                    } elseif (isset($initresult['no_cache'])) {
                        $block['nocache'] = $initresult['no_cache'];
                        $blocks[$key]['nocache'] = $initresult['no_cache'];
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

?>
