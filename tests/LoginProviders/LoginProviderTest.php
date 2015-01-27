<?php

namespace Gcd\Core\LoginProviders;

use Gcd\Core\UnitTesting\CoreTestCase;
use Gcd\Core\UnitTesting\UnitTestingLoginProvider;

class LoginProviderTest extends CoreTestCase
{
    public function testForceLogin()
    {
        $loginProvider = new UnitTestingLoginProvider();
        $loginProvider->ForceLogin();

        $this->assertTrue( $loginProvider->IsLoggedIn() );
    }
}
 