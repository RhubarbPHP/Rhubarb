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

    public abstract function createAssetFromFile($filePath);

    public abstract function getStream(Asset $asset);

    public abstract function getUrl(Asset $asset);

    public function createToken($data)
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
     * @return mixed
     */
    public static function getProvider($assetCategory = "")
    {
        $class = self::$providerMap[$assetCategory];

        $provider = new $class();

        return $provider;
    }
}