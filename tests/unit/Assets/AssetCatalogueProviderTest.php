<?php

namespace Rhubarb\Crown\Tests\Assets;

use Rhubarb\Crown\Assets\AssetCatalogueProvider;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class AssetCatalogueProviderTest extends RhubarbTestCase
{
    public function testProviderMapping()
    {
        AssetCatalogueProvider::setProviderClassName(TestAssetCatalogueProvider::class);
        $provider = AssetCatalogueProvider::getProvider();
        
        $this->assertInstanceOf(TestAssetCatalogueProvider::class, $provider);

        AssetCatalogueProvider::setProviderClassName(Test2AssetCatalogueProvider::class, "TestCategory");
        $provider = AssetCatalogueProvider::getProvider("TestCategory");
        
        $this->assertInstanceOf(Test2AssetCatalogueProvider::class, $provider);
    }
}

class TestAssetCatalogueProvider extends AssetCatalogueProvider
{

    public function createAssetFromFile($filePath)
    {
        // TODO: Implement createAssetFromFile() method.
    }

    public function getStream($token)
    {
        // TODO: Implement getStream() method.
    }

    public function getUrl($token)
    {
        // TODO: Implement getUrl() method.
    }
}

class Test2AssetCatalogueProvider extends TestAssetCatalogueProvider
{

}