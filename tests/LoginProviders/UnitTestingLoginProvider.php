<?php

namespace Rhubarb\Crown\Tests\LoginProviders;

use Rhubarb\Crown\LoginProviders\LoginProvider;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */
class UnitTestingLoginProvider extends LoginProvider
{
	public function Login()
	{
		$this->LoggedIn = true;
	}
}