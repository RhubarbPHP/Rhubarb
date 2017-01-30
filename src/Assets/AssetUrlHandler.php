<?php

namespace Rhubarb\Crown\Assets;

use Rhubarb\Crown\Application;
use Rhubarb\Crown\Exceptions\AssetExposureException;
use Rhubarb\Crown\Exceptions\StopGeneratingResponseException;
use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Response\Response;
use Rhubarb\Crown\UrlHandlers\UrlHandler;

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
     * @throws StopGeneratingResponseException
     */
    protected function generateResponseForRequest($request = null)
    {
        if (!$this->isPermitted()){
            throw new AssetExposureException($this->token);
        }

        // For performance reasons this handler has to forgo the normal response object
        // pattern and output directly to the client. This also means we have to use
        // the raw headers command with a warning suppression  to avoid unit tests breaking.
        if (!Application::current()->unitTesting) {
            while (ob_get_level()) {
                ob_end_clean();
            }
        }

        $asset = AssetCatalogueProvider::getAsset($this->token);

        @header("Content-type: ".$asset->mimeType, true);
        @header("Content-disposition: attachment; filename=\"".$asset->name."\"");
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
}