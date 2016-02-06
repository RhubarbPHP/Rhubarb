<?php

namespace Rhubarb\Crown\DependencyInjection;

interface ProviderInterface
{
    public static function setProviderClassName($providerClassName);

    public static function getProvider();
}