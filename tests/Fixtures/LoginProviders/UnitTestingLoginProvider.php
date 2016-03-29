<?php

namespace Rhubarb\Crown\Tests\Fixtures\LoginProviders;

use Rhubarb\Crown\LoginProviders\LoginProvider;

class UnitTestingLoginProvider extends LoginProvider
{
    public function login()
    {
        $this->LoggedIn = true;
    }
}
