<?php

namespace Rhubarb\Crown\Sendables;

/**
 * The base class for all sendable things.
 */
abstract class Sendable
{
    protected abstract function logSending();

    protected abstract function getProviderClassName();

    public final function send()
    {
        $this->logSending();

        $provider = $this->createProvider();
        $provider->send($this);
    }

    private function createProvider()
    {
        $providerClass = $this->getProviderClassName();
        $provider = $providerClass::getDefaultProvider();

        return $provider;
    }
}
