<?php

namespace Rhubarb\Crown\Tests\unit\Sessions;

use Rhubarb\Crown\Container;
use Rhubarb\Crown\Sessions\SessionProviders\PhpSessionProvider;
use Rhubarb\Crown\Sessions\SessionProviders\SessionProvider;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

/**
 *
 * Note for unit tests for loading and saving of sessions look to the
 * test cases for the individual session provider type.
 */
class SessionTest extends RhubarbTestCase
{
    public function testSessionGetsProvider()
    {
        Container::current()->registerClass(SessionProvider::class, UnitTestingSessionProvider::class);

        $session = UnitTestingSession::instance();

        $this->assertInstanceOf(UnitTestingSessionProvider::class, $session->testGetSessionProvider());

        Container::current()->registerClass(SessionProvider::class, PhpSessionProvider::class);

        // Although we have changed the default provider, we already instantiated the session so the provider will not
        // have changed
        $this->assertInstanceOf(UnitTestingSessionProvider::class, $session->testGetSessionProvider());
    }
}