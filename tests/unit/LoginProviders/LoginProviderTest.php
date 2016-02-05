<?php

namespace Rhubarb\Crown\Tests\unit\LoginProviders;

use Rhubarb\Crown\Tests\Fixtures\LoginProviders\UnitTestingLoginProvider;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class LoginProviderTest extends RhubarbTestCase
{
    public function testForceLogin()
    {
        $loginProvider = UnitTestingLoginProvider::instance();
        $loginProvider->forceLogin();

        $this->assertTrue($loginProvider->isLoggedIn());
    }
}
