<?php

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
     * @return string
     * @throws AssetException
     */
    private static function getJwtKey()
    {
        $settings = AssetCatalogueSettings::singleton();
        $key = $settings->jwtKey;

        if ($key == "") {
            throw new AssetException("No token key is defined in AssetCatalogueSettings");
        }
        return $key;
    }

    public abstract function createAssetFromFile($filePath);

    public abstract function getStream(Asset $asset);

    public abstract function getUrl($token);

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