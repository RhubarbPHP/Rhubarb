<?php

namespace Rhubarb\Crown\Assets;

class Asset
{
    /**
     * The canonical token that represents this asset.
     *
     * @var string
     */
    private $token;

    /**
     * @var AssetCatalogueProvider
     */
    private $sourceProvider;
    /**
     * @var array
     */
    private $providerData;

    public function __construct($token, AssetCatalogueProvider $sourceProvider, $providerData = [])
    {
        $this->token = $token;
        $this->sourceProvider = $sourceProvider;
        $this->providerData = $providerData;
    }

    public function writeToFile($filePath)
    {
        $out = fopen($filePath,"w");

        $stream = $this->getStream();

        while(!feof($stream)){
            $bytes = fread($stream, 8192);
            fwrite($out, $bytes);
        }

        fclose($stream);
        fclose($out);
    }

    public function getStream()
    {
        return $this->sourceProvider->getStream($this);
    }

    public function getUrl()
    {
        return $this->sourceProvider->getStream($this);
    }

    public function getProviderData()
    {
        return $this->providerData;
    }
}