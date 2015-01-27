<?php

namespace Gcd\Core\Sessions\UnitTesting;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */
class UnitTestingSession extends \Gcd\Core\Sessions\Session
{
	/**
	 * Simply exposes the protected GetSessionProvider() method.
	 */
	public function TestGetSessionProvider()
	{
		return $this->GetSessionProvider();
	}
}
