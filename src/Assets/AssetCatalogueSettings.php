<?php

namespace Rhubarb\Crown\Assets;

use Rhubarb\Crown\Settings;

/**
 * Settings related to asset catalogue provision.
 */
class AssetCatalogueSettings extends Settings
{
    /**
     * A string key to use for signing asset tokens (JWT tokens)
     *
     * @var string
     */
    public $jwtKey;
}