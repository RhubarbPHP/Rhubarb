<?php

namespace Rhubarb\Crown\Exceptions\Handlers;

use Rhubarb\Crown\Settings;

class ExceptionSettings extends Settings
{
    /**
     * True if exception trapping should be enabled
     *
     * @var bool
     */
    public $exceptionTrappingOn = true;
}