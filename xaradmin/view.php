<?php
/**
 * View cache items
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage CacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.cachemanager.class.info');
use Xaraya\Modules\CacheManager\CacheInfo;

/**
 * show the content of cache items
 * @param array $args with optional arguments:
 * - string $args['tab']
 * - string $args['key']
 * - string $args['code']
 * @return array|void
 */
function cachemanager_admin_view(array $args = [], $context = null)
{
    extract($args);

    if (!xarVar::fetch('tab', 'str', $tab, null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('key', 'str', $key, null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('code', 'str', $code, null, xarVar::NOT_REQUIRED)) {
        return;
    }

    if (empty($tab)) {
        xarController::redirect(xarController::URL('cachemanager', 'admin', 'stats'), null, $context);
        return;
    } elseif (empty($key)) {
        xarController::redirect(xarController::URL(
            'cachemanager',
            'admin',
            'stats',
            ['tab' => $tab]
        ), null, $context);
        return;
    }

    if (!xarSecurity::check('AdminXarCache')) {
        return;
    }

    $data = [];

    $data['tab'] = $tab;
    $data['key'] = $key;
    $data['code'] = $code;
    $data['lines'] = [];
    $data['title']  = '';
    $data['link']  = '';
    $data['styles'] = [];
    $data['script'] = [];

    $content = CacheInfo::getItem($tab, $key, $code);
    if (!empty($content)) {
        if ($tab == 'module' || $tab == 'object') {
            $data['lines']  = explode("\n", $content['output']);
            $data['title']  = $content['title'];
            $data['link']   = $content['link'];
            $data['styles'] = $content['styles'];
            $data['script'] = $content['script'];
        } elseif ($tab == 'variable') {
            $data['lines']  = explode("\n", print_r($content, true));
        } else {
            $data['lines'] = explode("\n", $content);
        }
    }

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSec::genAuthKey();
    $data['return_url'] = xarController::URL('cachemanager', 'admin', 'stats', ['tab' => $tab]);
    return $data;
}
