<?php

namespace Rhubarb\Crown\Tests\unit\Encryption;

use Rhubarb\Crown\Encryption\PlainTextHashProvider;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class PlainTextHashProviderTest extends RhubarbTestCase
{
    public function testProvider()
    {
        $plainTextProvider = new PlainTextHashProvider();
        $result = $plainTextProvider->createHash("abc123", "");

        $this->assertEquals("abc123", $result);

        $this->assertTrue($plainTextProvider->compareHash("abc123", "abc123"));
    }
}
