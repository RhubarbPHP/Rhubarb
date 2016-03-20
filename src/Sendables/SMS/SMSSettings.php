<?php

namespace Rhubarb\Crown\Sendables\SMS;

use Rhubarb\Crown\Settings;

/**
 * Container for some default properties for sending sms.
 *
 * @property SMSNumber|bool $OnlyRecipient If you wish to prevent a development setup from sending a real customer SMS, set this to a test recipient sms Number
 */
class SMSSettings extends Settings
{
    protected function initialiseDefaultValues()
    {
        parent::initialiseDefaultValues();

        $this->OnlyRecipient = false;
    }

}
