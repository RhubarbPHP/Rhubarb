<?php

namespace Rhubarb\Crown\DependencyInjection;

interface SingletonInterface
{
    public function singleton(...$arguments);
}