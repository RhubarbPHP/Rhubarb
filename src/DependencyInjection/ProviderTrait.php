<?php

namespace Rhubarb\Crown\DependencyInjection;

trait ProviderTrait
{
    public static function setProviderClassName($providerClassName)
    {
        Container::current()->registerClass(static::class, $providerClassName);
    }

    /**
     * @return static
     */
    public static function getProvider()
    {
        return Container::instance(static::class);
    }
}