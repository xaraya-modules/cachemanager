<?php
/**
 * Overview for cachemanager
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage CacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
/**
 * Overview displays standard Overview page
 * @return string
 */
function cachemanager_admin_overview(array $args = [], $context = null)
{
    $data = [];
    //just return to main function that displays the overview
    $data['context'] ??= $context;
    return xarTpl::module('cachemanager', 'admin', 'main', $data, 'main');
}
