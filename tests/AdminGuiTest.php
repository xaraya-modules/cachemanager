<?php

namespace Xaraya\Modules\CacheManager\Tests;

use Xaraya\Modules\TestHelper;
use Xaraya\Modules\CacheManager\AdminGui;
use xarController;
use xarMod;
use xarTpl;

final class AdminGuiTest extends TestHelper
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // we need to load templates to get some output
        xarTpl::init();
    }

    protected function setUp(): void {}

    protected function tearDown(): void {}

    public function testAdminGui(): void
    {
        $expected = AdminGui::class;
        $admingui = xarMod::getModule('cachemanager')->admingui();
        $this->assertEquals($expected, $admingui::class);
    }

    public function testCallStats(): void
    {
        $context = $this->createContext(['source' => __METHOD__]);
        /** @var AdminGui $admingui */
        $admingui = $this->createMockWithAccess('cachemanager', AdminGui::class, 1);
        $admingui->setContext($context);

        // this is required because Cachemanager admin_menu calls getstatus()
        // which does security check, and it redirects & exits
        xarController::setCallback('redirectTo', [$this, 'hello']);

        // use __call() here
        $args = ['hello' => 'world'];
        $data = $admingui->stats($args);

        xarController::setCallback('redirectTo', null);

        $expected = [
            'status',
            'tab',
            'itemsperpage',
            'settings',
            'pagecache',
            'blockcache',
            'modulecache',
            'objectcache',
            'variablecache',
            'querycache',
        ];
        $this->assertEquals($expected, array_keys($data));

        $expected = $context;
        $this->assertEquals($expected, $admingui->getContext());
    }

    public function hello($redirectURL, $httpResponse, $context)
    {
        // echo "We got: $redirectURL with " . json_encode($context);
    }
}
