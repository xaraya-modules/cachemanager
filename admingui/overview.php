<?php

/**
 * @package modules\cachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\CacheManager\AdminGui;

use Xaraya\Modules\CacheManager\AdminGui;
use Xaraya\Modules\MethodClass;

/**
 * cachemanager admin overview function
 * @extends MethodClass<AdminGui>
 */
class OverviewMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Overview displays standard Overview page
     * @return string
     * @see AdminGui::overview()
     */
    public function __invoke(array $args = [])
    {
        $data = [];
        //just return to main function that displays the overview
        $data['context'] ??= $this->getContext();
        return $this->mod()->template('main', $data, 'main');
    }
}
