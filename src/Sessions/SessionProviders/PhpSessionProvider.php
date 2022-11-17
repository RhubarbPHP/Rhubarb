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

namespace Rhubarb\Crown\Sessions\SessionProviders;

require_once __DIR__ . "/SessionProvider.php";

use Rhubarb\Crown\Application;
use Rhubarb\Crown\PhpContext;
use Rhubarb\Crown\Sessions\Session;

/**
 * A Session provider using a standard PHP session.
 */
class PhpSessionProvider extends SessionProvider
{
    public function __construct()
    {
        $context = Application::current()->context();

        if (!$context->isCliInvocation()) {
            // Ensure session cookie is http only and secure if we're on https
            $onSsl = ($_SERVER["REQUEST_SCHEME"] === "https" || (isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && $_SERVER["HTTP_X_FORWARDED_PROTO"] === "https"));
            session_set_cookie_params(0, '/', '', $onSsl, true);
        }
    }

    private $restored = false;

    public function restoreSession(Session $session)
    {
        $context = Application::current()->context();

        if (!$context->isCliInvocation()) {
            if (!$this->restored) {
                session_start();
            }
        }

        $namespace = get_class($session);

        if (isset($_SESSION[$namespace])) {
            $session->setSessionData($_SESSION[$namespace]);
        }

        // Close the session to make sure we aren't locking other process for this user, e.g.
        // simultaneous AJAX requests.
        if (!$context->isCliInvocation()) {
            if (!$this->restored) {
                session_write_close();
            }
        }

        $this->restored = true;
    }

    public function storeSession(Session $session)
    {
        $context = Application::current()->context();

        if (!$context->isCliInvocation()) {
            session_start();
        }

        $namespace = get_class($session);

        $_SESSION[$namespace] = $session->extractSessionData();

        // Close the session to make sure we aren't locking other process for this user, e.g.
        // simultaneous AJAX requests.
        if (!$context->isCliInvocation()) {
            session_write_close();
        }
    }
}
