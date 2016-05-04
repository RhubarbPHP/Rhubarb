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

namespace Rhubarb\Crown\Tests\unit\Sessions;

use Rhubarb\Crown\DependencyInjection\Container;
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

        $session = UnitTestingSession::singleton();

        $this->assertInstanceOf(UnitTestingSessionProvider::class, $session->testGetSessionProvider());

        Container::current()->registerClass(SessionProvider::class, PhpSessionProvider::class);

        // Although we have changed the default provider, we already instantiated the session so the provider will not
        // have changed
        $this->assertInstanceOf(UnitTestingSessionProvider::class, $session->testGetSessionProvider());
    }
}
