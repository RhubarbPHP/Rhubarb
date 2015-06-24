<?php

namespace Rhubarb\Crown\Tests\Sessions;

use Rhubarb\Crown\Sessions\Session;

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
