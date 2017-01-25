<?php

namespace Rhubarb\Crown\Tests\unit\Assets;

use Rhubarb\Crown\Assets\LocalFilesAssetCatalogueProvider;

class LocalFilesAssetCatalogueProviderTest extends AssetCatalogueProviderTests
{

    protected function getProvider()
    {
        return new LocalFilesAssetCatalogueProvider();
    }
}