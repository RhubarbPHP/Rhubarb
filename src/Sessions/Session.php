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

namespace Rhubarb\Crown\Sessions;

use Rhubarb\Crown\Container;
use Rhubarb\Crown\Sessions\Exceptions\SessionProviderNotFoundException;
use Rhubarb\Crown\Sessions\SessionProviders\SessionProvider;
use Rhubarb\Crown\Settings;

require_once __DIR__ . "/../Settings.php";

/**
 * A namespaced session management object.
 *
 * In most cases you should extend this class to provide effective ring fencing to your session
 * properties.
 *
 * Simply call StoreSession() to save the session. The session is restored upon first use automatically.
 *
 * @see StoreSession()
 */
class Session extends Settings
{
    /**
     * @var \Rhubarb\Crown\Sessions\SessionProviders\SessionProvider
     */
    private $sessionProvider = null;

    /**
     * Get's the SessionProvider for this session.
     *
     * To change the session provider for a session simply implement the GetNewSessionProvider() method.
     *
     * @return SessionProviders\SessionProvider
     */
    final protected function getSessionProvider()
    {
        if ($this->sessionProvider == null) {
            $this->sessionProvider = $this->getNewSessionProvider();
        }

        return $this->sessionProvider;
    }

    /**
     * Leverages the base method from Settings to restore the session.
     */
    protected function initialiseDefaultValues()
    {
        $provider = $this->getSessionProvider();
        $provider->restoreSession($this);

        parent::initialiseDefaultValues();
    }

    /**
     * Returns a new instance of the session provider used to store this session.
     *
     * Override this to replace the default behaviour of using the default provider class.
     *
     * @return SessionProviders\SessionProvider
     */
    protected function getNewSessionProvider()
    {
        return Container::instance(SessionProvider::class);
    }

    /**
     * Stores the session using the current Provider
     */
    public function storeSession()
    {
        $provider = $this->getSessionProvider();
        $provider->storeSession($this);
    }
}
