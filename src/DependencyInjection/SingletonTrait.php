<?php

namespace Rhubarb\Crown\DependencyInjection;

trait SingletonTrait
{
    /**
     * @param $arguments
     * @return static
     */
    final public static function singleton(...$arguments)
    {
        return Container::current()->registerSingleton(static::class, function() use ($arguments) {
            return new static(...$arguments);
        });
    }
}