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

namespace Rhubarb\Crown\Tests\Assets;

use Rhubarb\Crown\Assets\Asset;
use Rhubarb\Crown\Assets\AssetCatalogueProvider;
use Rhubarb\Crown\Assets\AssetCatalogueSettings;
use Rhubarb\Crown\Assets\LocalStorageAssetCatalogueProvider;
use Rhubarb\Crown\Assets\LocalStorageAssetCatalogueSettings;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class AssetCatalogueProviderTest extends RhubarbTestCase
{
    protected $file = __DIR__."/wrapper.txt";

    protected function setUp()
    {
        parent::setUp();

        $settings = AssetCatalogueSettings::singleton();
        $settings->jwtKey = "rhubarbphp";

        $settings = LocalStorageAssetCatalogueSettings::singleton();
        $settings->storageRootPath = __DIR__."/data";

        file_put_contents($this->file,"test");
    }


    public function testProviderMapping()
    {
        AssetCatalogueProvider::setProviderClassName(TestAssetCatalogueProvider::class);
        $provider = AssetCatalogueProvider::getProvider();
        
        $this->assertInstanceOf(TestAssetCatalogueProvider::class, $provider);

        AssetCatalogueProvider::setProviderClassName(Test2AssetCatalogueProvider::class, "TestCategory");
        $provider = AssetCatalogueProvider::getProvider("TestCategory");
        
        $this->assertInstanceOf(Test2AssetCatalogueProvider::class, $provider);
    }

    public function testStorageWrapper()
    {
        AssetCatalogueProvider::setProviderClassName(LocalStorageAssetCatalogueProvider::class, "test");

        $asset = AssetCatalogueProvider::storeAsset($this->file, "test");
        $stream = $asset->getStream();

        $content = stream_get_contents($stream);
        $this->assertEquals("test", $content);
    }

    public function testAssetProperties()
    {
        AssetCatalogueProvider::setProviderClassName(LocalStorageAssetCatalogueProvider::class, "test");

        $asset = AssetCatalogueProvider::storeAsset($this->file, "test");

        $this->assertEquals("text/plain; charset=us-ascii", $asset->mimeType);
        $this->assertEquals("wrapper.txt", $asset->name);
        $this->assertEquals(4, $asset->size);

        $asset = AssetCatalogueProvider::getAsset($asset->getToken());

        $this->assertEquals("text/plain; charset=us-ascii", $asset->mimeType);
        $this->assertEquals("wrapper.txt", $asset->name);
        $this->assertEquals(4, $asset->size);
    }

}

class TestAssetCatalogueProvider extends AssetCatalogueProvider
{

    public function createAssetFromFile($filePath, $commonProperties)
    {
    }

    public function getStream(Asset $asset)
    {
    }

    public function getUrl(Asset $asset)
    {
    }
}

class Test2AssetCatalogueProvider extends TestAssetCatalogueProvider
{

}