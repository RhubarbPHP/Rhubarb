<?php

namespace Rhubarb\Crown\Tests\LoginProviders;

use Rhubarb\Crown\Tests\RhubarbTestCase;

class LoginProviderTest extends RhubarbTestCase
{
    public function testForceLogin()
    {
        $loginProvider = new UnitTestingLoginProvider();
        $loginProvider->forceLogin();

        $this->assertTrue( $loginProvider->isLoggedIn() );
    }
}
 