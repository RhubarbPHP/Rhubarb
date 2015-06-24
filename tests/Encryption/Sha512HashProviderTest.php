<?php

namespace Rhubarb\Crown\Tests\Encryption;

use Rhubarb\Crown\Encryption\Sha512HashProvider;
use Rhubarb\Crown\Tests\RhubarbTestCase;

class Sha512HashProviderTest extends RhubarbTestCase
{
    public function testHash()
    {
        $hasher = new Sha512HashProvider();
        $result = $hasher->createHash("abc123", "saltyfish");

        $this->assertEquals('$6$rounds=10000$saltyfish$xsdN77OODY/XmxLdlkFW9CNxuE4H6NjEGG7K7tGJbzHUyDrVDHROL/FqG.ANet3dcd6WqGOOvaDjLv/WeAtcK0',
            $result);
    }

    public function testHashesAreCompared()
    {
        $hasher = new Sha512HashProvider();

        $hash = $hasher->createHash("abc123", "saltyfish");

        $result = $hasher->compareHash("abc123", $hash);
        $this->assertTrue($result);

        $result = $hasher->compareHash("dep456", $hash);
        $this->assertFalse($result);

        // Repeat the tests with an automated salt.
        $hash = $hasher->createHash("abc123");

        $result = $hasher->compareHash("abc123", $hash);
        $this->assertTrue($result);

        $result = $hasher->compareHash("dep456", $hash);
        $this->assertFalse($result);
    }
}
