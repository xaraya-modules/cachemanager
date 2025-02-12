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

use Xaraya\Modules\ModuleClass;

/**
 * Get cachemanager module classes via xarMod::getModule()
 */
class Module extends ModuleClass
{
    public function setClassTypes(): void
    {
        parent::setClassTypes();
        // add other class types for cachemanager
        $this->classtypes['statsapi'] = 'StatsApi';
    }

    public function getStatsAPI(): StatsApi
    {
        $component = $this->getComponent('StatsApi');
        assert($component instanceof StatsApi);
        return $component;
    }
}
