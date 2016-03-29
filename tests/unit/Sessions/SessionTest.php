<?php

namespace Rhubarb\Crown\Tests\unit\Sessions;

use Rhubarb\Crown\Sessions\Exceptions\SessionProviderNotFoundException;
use Rhubarb\Crown\Sessions\Session;
use Rhubarb\Crown\Sessions\SessionProviders\PhpSessionProvider;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

/**
 *
 * Note for unit tests for loading and saving of sessions look to the
 * test cases for the individual session provider type.
 */
class SessionTest extends RhubarbTestCase
{
    public function setUp()
    {
        Session::setDefaultSessionProviderClassName(PhpSessionProvider::class);

        parent::setUp();
    }

    public function testDefaultSessionProvider()
    {
        $this->assertEquals(PhpSessionProvider::class, Session::getDefaultSessionProviderClassName());

        Session::setDefaultSessionProviderClassName(UnitTestingSessionProvider::class);

        $this->assertEquals(UnitTestingSessionProvider::class, Session::getDefaultSessionProviderClassName());

        $this->setExpectedException(SessionProviderNotFoundException::class);

        Session::setDefaultSessionProviderClassName('\Rhubarb\Crown\Sessions\SessionProviders\UnknownProvider');
    }

    public function testSessionGetsProvider()
    {
        Session::setDefaultSessionProviderClassName(UnitTestingSessionProvider::class);

        $session = new UnitTestingSession();

        $this->assertInstanceOf(UnitTestingSessionProvider::class, $session->testGetSessionProvider());

        Session::setDefaultSessionProviderClassName(PhpSessionProvider::class);

        // Although we have changed the default provider, we already instantiated the session so the provider will not
        // have changed
        $this->assertInstanceOf(UnitTestingSessionProvider::class, $session->testGetSessionProvider());
    }
}
