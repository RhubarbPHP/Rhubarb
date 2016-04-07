<?php

namespace Rhubarb\Crown\Tests\Fixtures;

use Rhubarb\Crown\Sendables\Email\Email;
use Rhubarb\Crown\Sendables\Email\EmailProvider;
use Rhubarb\Crown\Sendables\Sendable;

class UnitTestingEmailProvider extends EmailProvider
{
    /**
     * @var Email
     */
    private static $_lastEmail;

    public function send(Sendable $email)
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
