<?php

namespace Rhubarb\Crown\Tests\Assets;

use Rhubarb\Crown\Assets\AssetCatalogueProvider;
use Rhubarb\Crown\Assets\AssetCatalogueSettings;
use Rhubarb\Crown\Assets\AssetUrlHandler;
use Rhubarb\Crown\Assets\LocalStorageAssetCatalogueProvider;
use Rhubarb\Crown\Assets\LocalStorageAssetCatalogueSettings;
use Rhubarb\Crown\Exceptions\StopGeneratingResponseException;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class AssetUrlHandlerTest extends RhubarbTestCase
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

        $request = new WebRequest();
        $request->urlPath = "/data/".$asset->getToken();

        $handler = new AssetUrlHandler("test");
        $handler->setUrl("/data/");

        ob_start();
        try {
            $handler->generateResponse($request);
        } catch( StopGeneratingResponseException $er){

        }

        $content = ob_get_clean();

        $this->assertEquals("url-test", $content);
    }
}