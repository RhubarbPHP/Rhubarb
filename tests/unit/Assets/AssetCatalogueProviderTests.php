<?php

namespace Rhubarb\Crown\Tests\unit\Assets;

use Rhubarb\Crown\Assets\AssetCatalogueProvider;
use Rhubarb\Crown\Assets\AssetCatalogueSettings;
use Rhubarb\Crown\Assets\LocalFilesAssetCatalogueSettings;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class AssetCatalogueProviderTests extends RhubarbTestCase
{
    /**
     * @return AssetCatalogueProvider
     */
    protected function getProvider()
    {
        return null;
    }

    public function testAssetIsStoredAndRetrieved()
    {
        $settings = AssetCatalogueSettings::singleton();
        $settings->jwtKey = "rhubarbphp";

        $settings = LocalFilesAssetCatalogueSettings::singleton();
        $settings->storageRootPath = __DIR__."/data";

        $content = uniqid();
        $file = __DIR__.'/test.txt';
        file_put_contents($file, $content);

        $provider = $this->getProvider();
        $token = $provider->createAssetFromFile($file);

        $asset = $provider->getAsset($token);
        $asset->writeToFile($file);

        $transferredContent = file_get_contents($file);

        $this->assertEquals($content, $transferredContent);
    }
}