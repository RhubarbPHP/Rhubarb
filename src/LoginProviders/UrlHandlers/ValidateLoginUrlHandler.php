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

namespace Rhubarb\Crown\LoginProviders\UrlHandlers;

require_once __DIR__ . "/../../UrlHandlers/UrlHandler.php";

use Rhubarb\Crown\Context;
use Rhubarb\Crown\LoginProviders\LoginProvider;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Crown\UrlHandlers\UrlHandler;

/**
 * A URL Handler that will check for a logged in user for the assigned portion of the URL tree.
 */
class ValidateLoginUrlHandler extends UrlHandler
{
    private $loginProvider;
    private $loginUrl;

    public function __construct(LoginProvider $loginProvider, $loginUrl, $children = [])
    {
        $this->loginProvider = $loginProvider;
        $this->loginUrl = $loginUrl;

        parent::__construct($children);
    }

    protected function generateResponseForRequest($request = null, $currentUrlFragment = "")
    {
        if (stripos($currentUrlFragment, $this->loginUrl) === 0) {
            return false;
        }

        if (!$this->loginProvider->isLoggedIn()) {

            $request = Context::currentRequest();
            $redirectUrl = $this->loginUrl;

            // Capture the existing URL to allow us to redirect to the original page.
            if ( $request->UrlPath != "/" ) {
                $url = base64_encode($request->UrlPath);

                $redirectUrl .= "?rd=".$url;
            }

            // This is a restricted path - redirect to the login page.
            $response = new RedirectResponse($redirectUrl);

            return $response;
        }

        return false;
    }
}