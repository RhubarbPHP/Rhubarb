<?php

namespace Rhubarb\Crown\Tests\LoginProviders;

use Rhubarb\Crown\LoginProviders\LoginProvider;

class UnitTestingLoginProvider extends LoginProvider
{
    public function Login()
    {
        $this->LoggedIn = true;
    }
}