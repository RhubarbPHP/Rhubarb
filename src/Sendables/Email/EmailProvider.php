<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Crown\Sendables\Email;
use Rhubarb\Crown\Sendables\SendableProvider;

/**
 * Performs transmission of emails to recipients.
 *
 * There is a default email provider which can be changed by calling the static setDefaultEmailProviderClassName()
 * function. Similarly to get the default email provider for your app simply call
 * EmailProvider::getDefaultEmailProvider()
 *
 * @see Email
 */
abstract class EmailProvider extends SendableProvider
{
    private static $defaultEmailProviderClassName = '\Rhubarb\Crown\Sendables\Email\PhpMailEmailProvider';

    public static function setDefaultEmailProviderClassName($emailProviderClassName)
    {
        self::$defaultEmailProviderClassName = $emailProviderClassName;
    }

    /**
     * Returns an instance of the default email provider
     *
     * @return EmailProvider
     */
    public static function getDefaultProvider()
    {
        $class = self::$defaultEmailProviderClassName;
        return new $class();
    }
}
