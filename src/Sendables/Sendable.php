<?php
/**
 * Copyright (c) 2016 RhubarbPHP.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
