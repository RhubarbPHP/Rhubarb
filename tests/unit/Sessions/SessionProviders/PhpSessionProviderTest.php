<?php

namespace Rhubarb\Crown\Tests\unit\Sessions\SessionProviders;

use Rhubarb\Crown\Settings;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Crown\Tests\unit\Sessions\UnitTestingSession;

class PhpSessionProviderTest extends RhubarbTestCase
{
    public function testSessionStorage()
    {
        $session = new UnitTestingSession();
        $session->TestValue = "abc123";
        $session->storeSession();

        $this->assertEquals("abc123", $_SESSION["UnitTestingSession"]["TestValue"]);
    }

    public function testSessionRestore()
    {
        $session = new UnitTestingSession();
        $session->TestValue = "abc123";
        $session->storeSession();

        // We can't test PHP sessions properly within the same script. However we can verify
        // that it at least restores the data from the $_SESSION array
        Settings::deleteSettingNamespace("UnitTestingSession");

        $session = new UnitTestingSession();

        $this->assertEquals("abc123", $session->TestValue);
    }
}
