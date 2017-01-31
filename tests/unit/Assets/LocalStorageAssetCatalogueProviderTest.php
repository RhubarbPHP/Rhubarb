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

namespace Rhubarb\Crown\Tests\unit\Assets;

use Rhubarb\Crown\Assets\AssetCatalogueSettings;
use Rhubarb\Crown\Assets\LocalStorageAssetCatalogueProvider;
use Rhubarb\Crown\Assets\LocalStorageAssetCatalogueProviderSettings;
use Rhubarb\Crown\Exceptions\AssetExposureException;

class LocalStorageAssetCatalogueProviderTest extends AssetCatalogueProviderTests
{
    protected function getProvider()
    {
        return new LocalStorageAssetCatalogueProvider();
    }

    public function testUrls()
    {
        $settings = AssetCatalogueSettings::singleton();
        $settings->jwtKey = "rhubarbphp";

        $settings = LocalStorageAssetCatalogueProviderSettings::singleton();
        $settings->storageRootPath = __DIR__."/data";
        $settings->rootUrl = "/data/";

        $content = uniqid();
        $file = __DIR__.'/test.txt';
        file_put_contents($file, $content);

        $provider = $this->getProvider();
        $asset = $provider->createAssetFromFile($file, []);

        $url = $asset->getUrl();

        $this->assertEquals("/data/".$asset->getProviderData()["file"], $url);

        $settings->rootUrl = "";


        try {
            $asset->getUrl();
            $this->fail("The asset is not exposable any more");
        } catch (AssetExposureException $er){

        }

        $asset->delete();
    }
}