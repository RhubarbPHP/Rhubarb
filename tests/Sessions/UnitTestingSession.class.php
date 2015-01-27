<?php

namespace Rhubarb\Crown\Sessions\UnitTesting;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */
class UnitTestingSession extends \Rhubarb\Crown\Sessions\Session
{
	/**
	 * Simply exposes the protected GetSessionProvider() method.
	 */
	public function TestGetSessionProvider()
	{
		return $this->GetSessionProvider();
	}
}
