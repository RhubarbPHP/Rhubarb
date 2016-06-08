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

namespace Rhubarb\Crown\Tests\unit\Sessions\SessionProviders;

use Rhubarb\Crown\DependencyInjection\Container;
use Rhubarb\Crown\Settings;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Crown\Tests\unit\Sessions\UnitTestingSession;

class PhpSessionProviderTest extends RhubarbTestCase
{
    public function testSessionStorage()
    {
        $session = UnitTestingSession::singleton();
        $session->TestValue = "abc123";
        $session->storeSession();

        $this->assertEquals("abc123", $_SESSION['Rhubarb\Crown\Tests\unit\Sessions\UnitTestingSession']["TestValue"]);
    }

    public function testSessionRestore()
    {
        $session = UnitTestingSession::singleton();
        $session->TestValue = "abc123";
        $session->storeSession();

        Container::current()->clearSingleton(UnitTestingSession::class);

        $session = UnitTestingSession::singleton();

        $this->assertEquals("abc123", $session->TestValue);
    }
}
