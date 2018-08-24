<?php

namespace Rhubarb\Crown\Assets;

class TempLocalStorageAssetCatalogueProvider extends LocalStorageAssetCatalogueProvider
{
    protected function getRootPathSetting()
    {
        return TEMP_DIR;
    }
}