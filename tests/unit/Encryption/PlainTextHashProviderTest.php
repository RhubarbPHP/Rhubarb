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
