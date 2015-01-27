<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Crown\Sessions\SessionProviders;

require_once __DIR__ . "/SessionProvider.php";

use Rhubarb\Crown\Context;
use Rhubarb\Crown\Sessions\Session;

/**
 * A Session provider using a standard PHP session.
 */
class PhpSessionProvider extends SessionProvider
{
    public function restoreSession(Session $session)
    {
        $context = new Context();

        if (!$context->IsCliInvocation) {
            session_start();
        }

        $namespace = $session->getNamespace();

        if (isset($_SESSION[$namespace])) {
            $session->importRawData($_SESSION[$namespace]);
        }

        // Close the session to make sure we aren't locking other process for this user, e.g.
        // simultaneous AJAX requests.
        if (!$context->IsCliInvocation) {
            session_write_close();
        }
    }

    public function storeSession(Session $session)
    {
        $context = new Context();

        if (!$context->IsCliInvocation) {
            session_start();
        }

        $namespace = $session->getNamespace();

        $_SESSION[$namespace] = $session->exportRawData();

        // Close the session to make sure we aren't locking other process for this user, e.g.
        // simultaneous AJAX requests.
        if (!$context->IsCliInvocation) {
            session_write_close();
        }
    }
}
