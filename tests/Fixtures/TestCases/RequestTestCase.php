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

namespace Rhubarb\Crown\Tests\Fixtures\TestCases;

use Rhubarb\Crown\Request\WebRequest;

class RequestTestCase extends RhubarbTestCase
{
    protected $stashSuperglobals = [];

    protected function setUp()
    {
        $this->stashSuperglobals['env'] = isset($_ENV) ? $_ENV : [];
        $this->stashSuperglobals['server'] = isset($_SERVER) ? $_SERVER : [];
        $this->stashSuperglobals['get'] = isset($_GET) ? $_GET : [];
        $this->stashSuperglobals['post'] = isset($_POST) ? $_POST : [];
        $this->stashSuperglobals['files'] = isset($_FILES) ? $_FILES : [];
        $this->stashSuperglobals['cookie'] = isset($_COOKIE) ? $_COOKIE : [];
        $this->stashSuperglobals['session'] = isset($_SESSION) ? $_SESSION : [];
        $this->stashSuperglobals['request'] = isset($_REQUEST) ? $_REQUEST : [];
    }

    protected function tearDown()
    {
        $_ENV = $this->stashSuperglobals['env'];
        $_SERVER = $this->stashSuperglobals['server'];
        $_GET = $this->stashSuperglobals['get'];
        $_POST = $this->stashSuperglobals['post'];
        $_FILES = $this->stashSuperglobals['files'];
        $_COOKIE = $this->stashSuperglobals['cookie'];
        $_SESSION = $this->stashSuperglobals['session'];
        $_REQUEST = $this->stashSuperglobals['request'];

        $this->stashSuperglobals = [];
    }
}
