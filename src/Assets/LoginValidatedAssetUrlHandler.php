<?php

namespace Rhubarb\Crown\Assets;

use Rhubarb\Crown\LoginProviders\LoginProvider;

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