<?php

namespace Rhubarb\Crown\Sendables;

/**
 * The base class for all sendable things.
 */
abstract class Sendable
{
    /**
     * Called when sending occurs providing an opportunity to log the event.
     * @return mixed
     */
    protected abstract function logSending();

    /**
     * Returns the name of the base provider class used to send this sendable
     *
     * @return string
     */
    protected abstract function getProviderClassName();

    /**
     * Returns the list of recipients for this sendable.@deprecated
     *
     * Sendable types must be able to return a list of recipients. This should return either an array
     * of values keyed by a string (e.g. email address) or a numerically indexed array containing
     * strings values (e.g. mobile phone numbers).
     *
     * If returning string keys the values can be of any type.
     *
     * @return array
     */
    public abstract function getRecipients();

    /**
     * Returns a common type for the sendable
     *
     * Used to understand which sendables are related. e.g. Email, SMS etc.
     *
     * @return string
     */
    public abstract function getSendableType();

    /**
     * Sendable types must be able to return a text representation of it's message body.
     *
     * This is used by sending frameworks to store and index outgoing communications.
     *
     * @return string
     */
    public abstract function getText();

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
