<?php

namespace Rhubarb\Crown\Tests\Fixtures;

use Rhubarb\Crown\Email\Email;
use Rhubarb\Crown\Email\EmailProvider;

class UnitTestingEmailProvider extends EmailProvider
{
    /**
     * @var Email
     */
    private static $_lastEmail;

    public function sendEmail(Email $email)
    {
        self::$_lastEmail = $email;
    }

    /**
     * @return Email
     */
    public static function getLastEmail()
    {
        return self::$_lastEmail;
    }
}
