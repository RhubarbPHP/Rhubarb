<?php

namespace Rhubarb\Crown\Tests\Assets;

use Rhubarb\Crown\Assets\AssetCatalogueProvider;
use Rhubarb\Crown\Assets\AssetCatalogueSettings;
use Rhubarb\Crown\Assets\LocalStorageAssetCatalogueProvider;
use Rhubarb\Crown\Assets\LocalStorageAssetCatalogueSettings;
use Rhubarb\Crown\Assets\LoginValidatedAssetUrlHandler;
use Rhubarb\Crown\Exceptions\AssetExposureException;
use Rhubarb\Crown\Exceptions\StopGeneratingResponseException;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Tests\Fixtures\LoginProviders\UnitTestingLoginProvider;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class LoginValidatedAssetUrlHandlerTest extends RhubarbTestCase
{
    public function testHandler()
    {
        $settings = AssetCatalogueSettings::singleton();
        $settings->jwtKey = "rhubarbphp";

        AssetCatalogueProvider::setProviderClassName(LocalStorageAssetCatalogueProvider::class, "test");

        $settings = LocalStorageAssetCatalogueSettings::singleton();
        $settings->storageRootPath = __DIR__."/data";

        $content = "url-test";
        $file = __DIR__.'/test.txt';

        file_put_contents($file, $content);

        $asset = AssetCatalogueProvider::storeAsset($file, "test");

        $login = UnitTestingLoginProvider::singleton();
        
        $request = new WebRequest();
        $request->urlPath = "/data/".$asset->getToken();

        $handler = new LoginValidatedAssetUrlHandler("test", UnitTestingLoginProvider::class);
        $handler->setUrl("/data/");

        try {
            $handler->generateResponse($request);
            $this->fail("We shouldn't have been allowed here");
        } catch( AssetExposureException $er){
        }

        $login->login();

        ob_start();

        try {
            $handler->generateResponse($request);
        } catch( AssetExposureException $er){
            $this->fail("We should have been allowed here");
        } catch (StopGeneratingResponseException $er){

        }

        ob_end_clean();
    }
}