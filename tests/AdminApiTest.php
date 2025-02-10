<?php

use Xaraya\Modules\TestHelper;
use Xaraya\Modules\CacheManager\AdminApi;

final class AdminApiTest extends TestHelper
{
    protected function setUp(): void
    {
        // file paths are relative to parent directory
        chdir(dirname(__DIR__));
    }

    protected function tearDown(): void {}

    public function testAdminApi(): void
    {
        $expected = AdminApi::class;
        $adminapi = xarMod::getModule('cachemanager')->adminapi();
        $this->assertEquals($expected, $adminapi::class);
    }

    public function testGetStatus(): void
    {
        $context = $this->createContext(['source' => __METHOD__]);
        /** @var AdminApi $adminapi */
        $adminapi = $this->createMockWithAccess('cachemanager', AdminApi::class, 1);
        $adminapi->setContext($context);

        $expected = [
            'CachingEnabled' => 0,
            'PageCachingEnabled' => 0,
            'AutoCachingEnabled' => 0,
            'BlockCachingEnabled' => 0,
            'ModuleCachingEnabled' => 0,
            'ObjectCachingEnabled' => 0,
            'VariableCachingEnabled' => 1,
            'CoreCachingEnabled' => 1,
            'QueryCachingEnabled' => 0,
        ];
        $status = $adminapi->getstatus();
        $this->assertEquals($expected, $status);
    }
}
