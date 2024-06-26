<?php
/**
 * CacheManager table setup
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage CacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 * @author jsb
*/

/**
 * Return cache tables
 * @return array
 */
function cachemanager_xartables()
{
    // Initialise table array
    $xartable = [];

    // Set the table names
    $xartable['cache_blocks'] = xarDB::getPrefix() . '_cache_blocks'; // cfr. blocks module
    $xartable['cache_data'] = xarDB::getPrefix() . '_cache_data';

    // Return the table information
    return $xartable;
}
