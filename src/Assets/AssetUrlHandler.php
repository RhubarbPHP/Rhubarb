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

use Rhubarb\Crown\Application;
use Rhubarb\Crown\Exceptions\AssetExposureException;
use Rhubarb\Crown\Exceptions\StopGeneratingResponseException;
use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Response\Response;
use Rhubarb\Crown\UrlHandlers\UrlHandler;

/**
 * A URL handler which can be registered to provide access to assets if public URLs can't be provisioned.
 */
class AssetUrlHandler extends UrlHandler
{
    /**
     * @var string
     */
    protected $assetCategory;

    protected $token;

    public function __construct($assetCategory, $childUrlHandlers = [])
    {
        parent::__construct($childUrlHandlers);

        $this->assetCategory = $assetCategory;
    }

    /**
     * Extend this class to provide conditional access by returning true or false.
     */
    protected function isPermitted()
    {
        return true;
    }

    protected function getMatchingUrlFragment(Request $request, $currentUrlFragment = "")
    {
        $uri = $currentUrlFragment;

        if (preg_match("|^" . rtrim($this->url, "/") . "/([^/]+)/?|", $uri, $match)) {
            $this->token = $match[1];
            return $match[0];
        }

        return false;
    }


    /**
     * Return the response if appropriate or false if no response could be generated.
     *
     * @param mixed $request
     * @return bool|Response
     * @throws AssetExposureException
     * @throws StopGeneratingResponseException
     */
    protected function generateResponseForRequest($request = null)
    {
        if (!$this->isPermitted()){
            throw new AssetExposureException($this->token);
        }

        $asset = $this->getAsset();

        // For performance reasons this handler has to forgo the normal response object
        // pattern and output directly to the client. This also means we have to use
        // the raw headers command with a warning suppression  to avoid unit tests breaking.
        if (!Application::current()->unitTesting) {
            while (ob_get_level()) {
                ob_end_clean();
            }
        }

        @header("Content-type: ".$asset->mimeType, true);
        @header("Content-disposition: filename=\"".$asset->name."\"");
        @header("Content-length: ".$asset->size);

        $stream = $asset->getStream();

        while (!feof($stream)) {
            $buffer = fread($stream, 8192);
            echo $buffer;
            flush();
        }

        fclose($stream);

        throw new StopGeneratingResponseException();
    }

    /**
     * Gets the asset for the current URL
     *
     * @return Asset
     * @throws AssetExposureException
     */
    protected function getAsset()
    {
        $asset = AssetCatalogueProvider::getAsset($this->token);

        if ($this->assetCategory != $asset->getProviderData()["category"]) {
            throw new AssetExposureException($this->token);
        }
        return $asset;
    }
}