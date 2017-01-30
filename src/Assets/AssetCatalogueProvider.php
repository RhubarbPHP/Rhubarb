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

use Firebase\JWT\JWT;
use Rhubarb\Crown\Exceptions\AssetException;

abstract class AssetCatalogueProvider
{
    private static $providerMap = [];

    /**
     * @var string
     */
    protected $category;

    public function __construct($category = "")
    {
        $this->category = $category;
    }

    /**
     * Stores an asset currently held in a local file in a given asset category
     *
     * @param string $filePath
     * @param string $category
     * @return Asset
     */
    public static function storeAsset($filePath, $category)
    {
        // A sane default
        $mime = "application/octet-stream";

        if (function_exists("finfo_open")){
            $info = new \finfo(FILEINFO_MIME);
            $mime = $info->file($filePath);
        }

        $provider = self::getProvider($category);
        $asset = $provider->createAssetFromFile($filePath, [
            "name" => basename($filePath),
            "size" => filesize($filePath),
            "mimeType" => $mime
        ]);

        $asset->mimeType = $mime;

        return $asset;
    }

    /**
     * Gets the key used to sign JWT tokens.
     * @return string
     * @throws AssetException
     */
    private static function getJwtKey()
    {
        $settings = AssetCatalogueSettings::singleton();
        $key = $settings->jwtKey;

        if ($key == "") {
            throw new AssetException("", "No token key is defined in AssetCatalogueSettings");
        }

        return $key;
    }

    /**
     * Creates an asset by loading from the given file path.
     *
     * @param $filePath
     * @param $commonProperties
     * @return Asset
     */
    public abstract function createAssetFromFile($filePath, $commonProperties);

    /**
     * Gets a PHP resource stream to allow reading the asset in chunks
     * @param Asset $asset
     * @return mixed
     */
    public abstract function getStream(Asset $asset);

    /**
     * If exposable, returns a URL for giving to the client for fetching the asset
     *
     * @param Asset $asset
     * @return string
     */
    public abstract function getUrl(Asset $asset);

    /**
     * Creates a JWT token to encode an asset using the passed data array.
     *
     * @param $data
     * @return string
     * @throws AssetException
     */
    protected function createToken($data)
    {
        $key = self::getJwtKey();

        $token = array(
            "iat" => time(),
            "provider" => get_class($this),
            "category" => $this->category,
            "data" => $data
        );

        $jwt = JWT::encode($token, $key);

        return $jwt;
    }

    /**
     * Recreates an asset from the data in the passed token.
     *
     * @param $token
     * @return Asset
     * @throws AssetException
     */
    public static function getAsset($token)
    {
        $key = self::getJwtKey();

        $payload = JWT::decode($token, $key, array('HS256'));

        $providerClass = $payload->provider;
        $category = $payload->category;
        $data = (array) $payload->data;

        $provider = new $providerClass($category);

        return new Asset($token, $provider, $data);
    }

    /**
     * Sets the asset provider for a given category.
     *
     * @param string $providerClassName The class to register as the provider
     * @param string $assetCategory The category of provider - or empty for the default provider
     */
    public static function setProviderClassName($providerClassName, $assetCategory = "")
    {
        self::$providerMap[$assetCategory] = $providerClassName;
    }

    /**
     * Returns an instance of the correct provider for a given category
     *
     * @param string $assetCategory The category of provider - or empty for the default provider
     * @return AssetCatalogueProvider
     */
    public static function getProvider($assetCategory = "")
    {
        $class = self::$providerMap[$assetCategory];

        $provider = new $class();

        return $provider;
    }
}