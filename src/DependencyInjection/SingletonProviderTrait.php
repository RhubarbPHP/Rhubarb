<?php

namespace Rhubarb\Crown\DependencyInjection;

trait SingletonProviderTrait
{
    use ProviderTrait;

    public static function setProviderClassName($providerClassName)
    {
        Container::current()->registerClass(static::class, $providerClassName, true);
    }
}