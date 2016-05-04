<?php
/**
 * Copyright (c) 2016 RhubarbPHP.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Rhubarb\Crown\Tests\unit\Encryption;

use Rhubarb\Crown\Encryption\Sha512HashProvider;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class Sha512HashProviderTest extends RhubarbTestCase
{
    public function testHash()
    {
        $hasher = new Sha512HashProvider();
        $result = $hasher->createHash("abc123", "saltyfish");

        $this->assertEquals(
            '$6$rounds=10000$saltyfish$xsdN77OODY/XmxLdlkFW9CNxuE4H6NjEGG7K7tGJbzHUyDrVDHROL/FqG.ANet3dcd6WqGOOvaDjLv/WeAtcK0',
            $result
        );
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
