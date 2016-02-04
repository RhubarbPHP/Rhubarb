<?php

namespace Rhubarb\Crown\Sendables;

/**
 * The base class of all sendable providers
 */
abstract class SendableProvider
{
    public abstract function send(Sendable $sendable);
}
