<?php
/**
 * CacheManager version information
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage CacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 * @author jsb | mikespub
 */
$modversion['name']           = 'cachemanager';
$modversion['id']             = '1652';
$modversion['version']        = '2.5.1';
$modversion['displayname']    = xarMLS::translate('CacheManager');
$modversion['description']    = 'Manage the output cache system of Xaraya';
$modversion['credits']        = '';
$modversion['help']           = '';
$modversion['changelog']      = '';
$modversion['license']        = '';
$modversion['official']       = true;
$modversion['author']         = 'jsb | mikespub';
$modversion['contact']        = 'http://www.xaraya.com/';
$modversion['admin']          = true;
$modversion['user']           = false;
$modversion['securityschema'] = ['CacheManager::' => '::'];
$modversion['class']          = 'Utility';
$modversion['category']       = 'Miscellaneous';
$modversion['namespace']      = 'Xaraya\Modules\CacheManager';
$modversion['twigtemplates']  = true;
$modversion['dependencyinfo'] = [
    0 => [
        'name' => 'Xaraya Core',
        'version_ge' => '2.4.1',
    ],
];
