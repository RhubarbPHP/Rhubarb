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
use Rhubarb\Crown\Exceptions\AssetException;
use Rhubarb\Crown\Exceptions\AssetExposureException;
use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\Exceptions\StopGeneratingResponseException;
use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Response\NotAuthorisedResponse;
use Rhubarb\Crown\Response\NotFoundResponse;
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

    /**
     * @var bool True if the handler should fail over to a default asset if the one requested isn't servable.
     */
    public $failOver = true;

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
        try {
            if (!$this->isPermitted()) {
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

            $stream = $asset->getStream();

            if (!Application::current()->unitTesting) {
                @header("Content-type: " . $asset->mimeType, true);
                @header("Content-disposition: filename=\"" . $asset->name . "\"");
                @header("Content-length: " . $asset->size);
            }
        } catch (AssetException $er){
            if ($this->failOver) {
                $image = $this->getMissingAssetDetails();
                if ($image) {
                    $stream = $image["stream"];

                    @header("Content-type: " . $image["mimeType"], true);
                    @header("Content-length: " . $image["size"]);
                } else {
                    throw new ForceResponseException(new NotFoundResponse());
                }
            } else {
                throw new ForceResponseException(new NotFoundResponse());
            }
        }

        $this->streamToOutput($stream);

        fclose($stream);

        // We need to guarantee that no headers are output after this point. Unfortunately the
        // only fool proof way is to exit PHP. We do fence for unit testing however to stop it
        // stopping a test suite.
        if (!Application::current()->unitTesting) {
            exit;
        }
    }

    protected function streamToOutput($stream)
    {
        while (!feof($stream)) {
            $buffer = fread($stream, 8192);
            echo $buffer;
            flush();
        }
    }

    /**
     * Returns the stream to be used whenever the handler cannot find or return the asset.*
     *
     * Return null to push a 404 response. Otherwise return an array with the following keys:
     *
     * stream => A PHP stream resource handle
     * size => The size of the image in bytes
     * mimeType => The mime type of the image
     */
    protected function getMissingAssetDetails()
    {
        $handle = fopen("php://memory","rw");
        fwrite($handle,base64_decode("iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII"));
        fseek($handle,0);

        return [
            "stream" => $handle,
            "size" => 68,
            "mimeType" => "image/png"
            ];
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