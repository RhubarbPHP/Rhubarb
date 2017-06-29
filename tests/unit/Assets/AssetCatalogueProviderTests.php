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

use Rhubarb\Crown\Assets\AssetCatalogueProvider;
use Rhubarb\Crown\Assets\AssetCatalogueSettings;
use Rhubarb\Crown\Assets\LocalStorageAssetCatalogueProviderSettings;
use Rhubarb\Crown\Exceptions\AssetNotFoundException;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class AssetCatalogueProviderTests extends RhubarbTestCase
{
    const TEST_CATEGORY = 'test';

    /**
     * @return AssetCatalogueProvider
     */
    protected function getProvider()
    {
        return null;
    }

    public function testAssetIsStoredAndRetrievedAndDeleted()
    {
        $settings = AssetCatalogueSettings::singleton();
        $settings->jwtKey = "rhubarbphp";

        $settings = LocalStorageAssetCatalogueProviderSettings::singleton();
        $settings->storageRootPath = __DIR__."/data";

        $content = uniqid();
        $file = __DIR__.'/test.txt';
        file_put_contents($file, $content);

        $provider = $this->getProvider();
        $asset = $provider->createAssetFromFile($file, ["category" => self::TEST_CATEGORY]);
        $token = $asset->getToken();

        $asset = $provider->getAsset($token);
        $asset->writeToFile($file);

        $transferredContent = file_get_contents($file);

        $this->assertEquals($content, $transferredContent);

        $asset->delete();

        try {
            $asset = $provider->getAsset($token);
            $asset->getStream();
            $this->fail("The asset should no longer be available - it's been deleted");
        } catch (AssetNotFoundException $er){
        }

        try {
            $asset->delete();
            $this->fail("The asset should no be deleteable any more.");
        } catch (AssetNotFoundException $er){

        }
    }
}