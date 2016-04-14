<?php

namespace Rhubarb\Crown\Sendables;

/**
 * The base class of all sendable providers
 */
abstract class SendableProvider
{
    /**
     * Sends the sendable.
     *
     * Implemented by the concrete provider type.
     *
     * @param Sendable $sendable
     * @return mixed
     */
    public abstract function send(Sendable $sendable);

    /**
     * Selects the currently registered concrete provider for the sendable's base type of provider and
     * passes it the sendable for sending.
     *
     * @param Sendable $sendable
     */
    public static function selectProviderAndSend(Sendable $sendable)
    {
        $provider = self::createProviderForSendable($sendable);
        $provider->send($sendable);
    }

    /**
     * @param Sendable $sendable
     * @return SendableProvider
     */
    private static function createProviderForSendable(Sendable $sendable)
    {
        $providerClass = $sendable->getProviderClassName();
        $provider = $providerClass::getProvider();

        return $provider;
    }
}
