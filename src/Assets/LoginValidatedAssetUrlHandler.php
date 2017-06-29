<?php

/**
 * Copyright (c) 2017 RhubarbPHP.
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

namespace Rhubarb\Crown\Assets;

use Rhubarb\Crown\LoginProviders\LoginProvider;

/**
 * An extension of AssetUrlHandler which adds login validation before granting permission.
 */
class LoginValidatedAssetUrlHandler extends AssetUrlHandler
{
    /**
     * @var array
     */
    private $loginProviderClassName;

    /**
     * LoginValidatedAssetUrlHandler constructor.
     * @param array $assetCategory
     * @param string $loginProviderClassName The login provider class name or empty string to use the application default
     * @param array $childUrlHandlers
     */
    public function __construct($assetCategory, $loginProviderClassName = "", $childUrlHandlers = [])
    {
        parent::__construct($assetCategory, $childUrlHandlers);
        
        $this->loginProviderClassName = $loginProviderClassName;
    }

    protected function isPermitted()
    {
        $providerClass = $this->loginProviderClassName;
        
        if ($providerClass != ""){
            $provider = $providerClass::singleton();
        } else {
            $provider = LoginProvider::getProvider();
        }
        
        return $provider->isLoggedIn();
    }

}