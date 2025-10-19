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

namespace Xaraya\Modules\CacheManager;

class Version
{
    /**
     * Get module version information
     *
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'name' => 'cachemanager',
            'id' => '1652',
            'version' => '2.5.1',
            'displayname' => 'CacheManager',
            'description' => 'Manage the output cache system of Xaraya',
            'credits' => '',
            'help' => '',
            'changelog' => '',
            'license' => '',
            'official' => true,
            'author' => 'jsb | mikespub',
            'contact' => 'http://www.xaraya.com/',
            'admin' => true,
            'user' => false,
            'securityschema'
             => [
                 'CacheManager::' => '::',
             ],
            'class' => 'Utility',
            'category' => 'Miscellaneous',
            'namespace' => 'Xaraya\\Modules\\CacheManager',
            'twigtemplates' => true,
            'dependencyinfo'
             => [
                 0
                  => [
                      'name' => 'Xaraya Core',
                      'version_ge' => '2.4.1',
                  ],
             ],
        ];
    }
}
