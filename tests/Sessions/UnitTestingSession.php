<?php

namespace Rhubarb\Crown\Tests\Sessions;

use Rhubarb\Crown\Sessions\Session;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */
class UnitTestingSession extends Session
{
    /**
     * Simply exposes the protected GetSessionProvider() method.
     */
    public function testGetSessionProvider()
    {
        return $this->getSessionProvider();
    }
}
