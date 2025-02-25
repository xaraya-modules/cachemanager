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
function cachemanager_xartables(?string $prefix = null)
{
    // Initialise table array
    $xartable = [];
    $prefix ??= xarDB::getPrefix();

    // Set the table names
    $xartable['cache_blocks'] = $prefix . '_cache_blocks'; // cfr. blocks module
    $xartable['cache_data'] = $prefix . '_cache_data';

    // Return the table information
    return $xartable;
}
