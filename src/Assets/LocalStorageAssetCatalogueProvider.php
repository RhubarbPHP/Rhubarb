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

use Rhubarb\Crown\Exceptions\AssetException;
use Rhubarb\Crown\Exceptions\AssetExposureException;
use Rhubarb\Crown\Exceptions\AssetNotFoundException;
use Rhubarb\Crown\Exceptions\SettingMissingException;
use Rhubarb\Crown\Logging\Log;

/**
 * A provider of assets storing files in a local directory store.
 */
class LocalStorageAssetCatalogueProvider extends AssetCatalogueProvider
{
    protected function getRootPathSetting(){
        $settings = LocalStorageAssetCatalogueProviderSettings::singleton();
        $rootFolder = $settings->storageRootPath;
        return $rootFolder;
    }
    private function getRootPath()
    {
        $rootFolder = $this->getRootPathSetting();
        if ($rootFolder == ""){
            throw new SettingMissingException("LocalStorageAssetCatalogueSettings", "storageRootPath");
        }

        if (!file_exists($rootFolder)){
            // Try to make the folder
            mkdir($rootFolder,0777, true);
        }

        if (!file_exists($rootFolder)){
            throw new AssetException("", "LocalStorageAssetCatalogueProvider could not find or create the root storage directory ".$rootFolder);
        }

        return $rootFolder;
    }

    /**
     * Makes the category name safe to use for a file path but guaranteed unique.
     *
     * e.g. This! and This@ should both work.
     */
    private function getCategoryDirectory()
    {
        if (!$this->category){
            return "_default";
        }

        $category = preg_replace("/\\W/","-",$this->category);
        $category = preg_replace("/-+/","-",$category);
        $category .= crc32($this->category);

        return $category;
    }

    public function createAssetFromFile($filePath, $commonProperties)
    {
        $root = $this->getRootPath();

        $uniqueString = uniqid();
        $uniqueFolder = substr($uniqueString,-2);
        $uniquePrefix = substr($uniqueString,0,-2);
        $newName = $uniqueFolder."/".$uniquePrefix."_".basename($filePath);

        $path = $root."/".$this->getCategoryDirectory();

        if (!file_exists($path."/".$uniqueFolder)){
            mkdir($path."/".$uniqueFolder, 0777, true);
        }

        // Shift the asset to it's new home.
        rename($filePath, $path."/".$newName);

        // Create the token.
        $commonProperties["file"] = $newName;

        $token = $this->createToken($commonProperties);
        $asset = new Asset($token, $this, $commonProperties);

        return $asset;
    }

    public function getStream(Asset $asset)
    {
        $path = $this->getAssetPath($asset);

        if (!file_exists($path)){
            Log::error("The LocalStorageAssetCatalogueProvider could not recover the asset at path ".$path, "ASSETS");

            throw new AssetNotFoundException($asset->getToken(), "An asset could not be found. For details of the asset location please review the error log.");
        }

        $handle = fopen($path, "r");

        return $handle;
    }

    public function getUrl(Asset $asset)
    {
        $settings = LocalStorageAssetCatalogueProviderSettings::singleton();

        if (!$settings->rootUrl){
            throw new AssetExposureException($asset->getToken());
        }

        return rtrim($settings->rootUrl, '/').'/'.$asset->getProviderData()["file"];
    }

    public function deleteAsset(Asset $asset)
    {
        $path = $this->getAssetPath($asset);

        if (!file_exists($path)){
            throw new AssetNotFoundException($asset->getToken());
        }

        unlink($path);
    }

    /**
     * Returns the local path for the given asset.
     *
     * @param Asset $asset
     * @return string
     */
    protected function getAssetPath(Asset $asset):string
    {
        // Get the file name from the provider data
        $data = $asset->getProviderData();
        $path = $this->getRootPath() . "/" . $this->getCategoryDirectory() . "/" . $data["file"];
        return $path;
    }
}