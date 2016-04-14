<?php

namespace Rhubarb\Crown\DependencyInjection;

interface SingletonInterface
{
    public static function singleton(...$arguments);
}