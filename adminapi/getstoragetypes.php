<?php

/**
 * @package modules\cachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\CacheManager\AdminApi;

use Xaraya\Modules\CacheManager\AdminApi;
use Xaraya\Modules\MethodClass;
use sys;

sys::import('xaraya.modules.method');

/**
 * cachemanager adminapi getstoragetypes function
 * @extends MethodClass<AdminApi>
 */
class GetstoragetypesMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     *
     * @author jsb
     * @return array Storage types, with key set to storage type and value set to its settings
     * @see AdminApi::getstoragetypes()
     */
    public function __invoke(array $args = [])
    {
        static $storagetypes;
        if (!empty($storagetypes)) {
            return $storagetypes;
        }

        $storagetypes = [];
        $storagetypes['filesystem']   = ['name'    => 'filesystem',
            'label'   => 'Filesystem',
            'enabled' => true, ];
        $storagetypes['database']     = ['name'    => 'database',
            'label'   => 'Database',
            'enabled' => true, ];
        $storagetypes['apcu']         = ['name'    => 'apcu',
            'label'   => 'APC User Cache (APCu)',
            'enabled' => function_exists('apcu_fetch') ? true : false, ];
        $storagetypes['doctrine']     = ['name'    => 'doctrine',
            'label'   => 'Doctrine Cache (via composer)',
            'enabled' => class_exists('Doctrine\\Common\\Cache\\CacheProvider') ? true : false, ];
        /**
        $storagetypes['eaccelerator'] = ['name'    => 'eaccelerator',
                                              'label'   => 'eAccelerator',
                                              'enabled' => function_exists('eaccelerator_get') ? true : false, ];
         */
        $storagetypes['memcached']    = ['name'    => 'memcached',
            'label'   => 'Memcached Server(s)',
            'enabled' => class_exists('Memcache') ? true : false, ];
        /**
        $storagetypes['mmcache']      = ['name'    => 'mmcache',
                                              'label'   => 'Turck MMCache',
                                              'enabled' => function_exists('mmcache_get') ? true : false, ];
         */
        /**
        $storagetypes['predis']       = ['name'    => 'predis',
                                              'label'   => 'Redis Server(s) (via composer)',
                                              'enabled' => class_exists('Predis\\Client') ?  true : false, ];
         */
        /**
        $storagetypes['redis']        = ['name'    => 'redis',
                                              'label'   => 'Redis Server(s) (extension)',
                                              'enabled' => class_exists('Redis') ? true : false, ];
         */
        /**
        $storagetypes['xcache']       = ['name'    => 'xcache',
                                              'label'   => 'XCache',
                                              'enabled' => function_exists('xcache_get') ? true : false, ];
         */
        $storagetypes['dummy']        = ['name'    => 'dummy',
            'label'   => 'Dummy Storage',
            'enabled' => false, ];

        // return the storage types and their settings
        return $storagetypes;
    }
}
