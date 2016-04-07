<?php

namespace Rhubarb\Crown\Sendables;

/**
 * The base class for all sendable things.
 */
abstract class Sendable
{
    /**
     * The list of recipients
     * @var SendableRecipient[]
     */
    protected $recipients = [];

    /**
     * Returns the name of the base provider class used to send this sendable
     *
     * @return string
     */
    public abstract function getProviderClassName();

    /**
     * Returns the list of recipients objects for this sendable.
     *
     * @return SendableRecipient[]
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * Clears the list of recipients
     */
    public function clearRecipients()
    {
        $this->recipients = [];
    }

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

    /**
     * Expresses the sendable as an array allowing it to be serialised, stored and recovered later.
     *
     * @return array
     */
    public abstract function toArray();

    public function addRecipient(SendableRecipient $recipient)
    {
        foreach($this->recipients as $existingRecipient){
            if ((string)$existingRecipient == (string)$recipient){
                return;
            }
        }

        $this->recipients[] = $recipient;
    }
}
