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

use Xaraya\Modules\CacheManager\CacheConfig;

class TemplateCache extends CacheConfig
{
    public static function init(array $args = []) {}

    /**
     * configure template caching (TODO)
     * @return array|void
     */
    public function modifyConfig($args)
    {
        extract($args);

        if (!$this->sec()->checkAccess('AdminXarCache')) {
            return;
        }

        $data = [];

        $this->var()->get('submit', $submit, 'str', '');
        if (!empty($submit)) {
            // Confirm authorisation code
            if (!$this->sec()->confirmAuthKey()) {
                return;
            }

            //$this->ctl()->redirect($this->mod()->getURL('admin', 'templates'));
            //return true;
        }

        // Get some template caching configurations
        $data['templates'] = $this->getConfig();

        $data['authid'] = $this->sec()->genAuthKey();
        return $data;
    }

    /**
     * get configuration of template caching
     *
     * @todo currently unsupported
     * @return array of template caching configurations
     */
    public function getConfig()
    {
        $templates = [];

        return $templates;
    }
}
