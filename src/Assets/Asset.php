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

/**
 * A representation of an asset which proxies support for reading the asset or returning a URL
 */
class Asset
{
    /**
     * The canonical token that represents this asset.
     *
     * @var string
     */
    private $token;

    /**
     * The provider who created the asset.
     *
     * @var AssetCatalogueProvider
     */
    private $sourceProvider;

    /**
     * An array of data important only to the source provider
     *
     * @var array
     */
    private $providerData;

    public function __construct($token, AssetCatalogueProvider $sourceProvider, $providerData = [])
    {
        $this->token = $token;
        $this->sourceProvider = $sourceProvider;
        $this->providerData = $providerData;
    }

    /**
     * Write the asset out to the corresponding file path.
     *
     * @param $filePath
     * @throws AssetException
     */
    public function writeToFile($filePath)
    {
        $out = @fopen($filePath,"w");

        if (!$out){
            throw new AssetException($this->token,
                "The asset '".$this->token."' could not be written to path '$filePath'. Check the path is valid and the directory exists.");
        }

        $stream = $this->getStream();

        if (!$stream){
            throw new AssetException($this->token,
                "The asset '".$this->token."' could not provide a valid stream.'");
        }

        while(!feof($stream)){
            $bytes = fread($stream, 8192);
            fwrite($out, $bytes);
        }

        fclose($stream);
        fclose($out);
    }

    /**
     * Gets a stream handle for the asset allowing for efficient streaming to another stream
     *
     * @return mixed
     */
    public function getStream()
    {
        return $this->sourceProvider->getStream($this);
    }

    /**
     * Gets a URL to allow clients to access the asset.
     *
     * Note that some assets are private and will not allow a public URL to be exposed. Where this happens
     * you should expect a AssetExposureException
     *
     * @return mixed
     */
    public function getUrl()
    {
        return $this->sourceProvider->getUrl($this);
    }

    /**
     * Gets the provider data for this asset.
     *
     * @return array
     */
    public function getProviderData()
    {
        return $this->providerData;
    }

    /**
     * Gets the token for this asset.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}