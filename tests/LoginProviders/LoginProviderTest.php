<?php

namespace Rhubarb\Crown\LoginProviders;

use Rhubarb\Crown\UnitTesting\RhubarbTestCase;
use Rhubarb\Crown\UnitTesting\UnitTestingLoginProvider;

class LoginProviderTest extends RhubarbTestCase
{
    public function testForceLogin()
    {
        $loginProvider = new UnitTestingLoginProvider();
        $loginProvider->ForceLogin();

        $this->assertTrue( $loginProvider->IsLoggedIn() );
    }
}
 