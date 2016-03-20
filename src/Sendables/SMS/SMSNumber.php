<?php

namespace Rhubarb\Crown\Sendables\SMS;

use Rhubarb\Crown\Exceptions\SMSException;

class SMSNumber
{
    /**
     * @var string The sms number e.g. +447710123123
     */
    public $number;
    /**
     * @var string The name e.g. John Smith
     */
    public $name;

    public function __construct($number, $name = "")
    {
        //  The number must be numeric to be able to send the SMS
        if (is_numeric($number)) {
            $this->number = $number;
        } else {
            throw new SMSException("The sms " . $number . " is not a valid number.");
        }

        if ($name != "") {
            $this->name = $name;
        }
    }
}
