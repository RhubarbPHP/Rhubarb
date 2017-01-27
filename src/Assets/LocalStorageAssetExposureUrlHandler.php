<?php

namespace Rhubarb\Crown\Assets;

use Rhubarb\Crown\Response\Response;
use Rhubarb\Crown\UrlHandlers\UrlHandler;

/**
 * Handles
 */
class LocalStorageAssetExposureUrlHandler extends UrlHandler
{
    /**
     * @var array
     */
    private $assetCatalogueProviderClassName;

    public function __construct($assetCatalogueProviderClassName, array $childUrlHandlers)
    {
        parent::__construct($childUrlHandlers);

        $this->assetCatalogueProviderClassName = $assetCatalogueProviderClassName;
    }

    /**
     * Return the response if appropriate or false if no response could be generated.
     *
     * @param mixed $request
     * @return bool|Response
     */
    protected function generateResponseForRequest($request = null)
    {

    }
}