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

namespace Rhubarb\Crown\Email;

use Rhubarb\Crown\Application;
use Rhubarb\Crown\Container;

/**
 * Performs transmission of emails to recipients.
 *
 * There is a default email provider which can be changed by calling the static setDefaultEmailProviderClassName()
 * function. Similarly to get the default email provider for your app simply call
 * EmailProvider::getDefaultEmailProvider()
 *
 * @see Email
 */
abstract class EmailProvider
{
    /**
     * @deprecated Use the dependency injection container instead
     * @param $emailProviderClassName
     */
    public static function setDefaultEmailProviderClassName($emailProviderClassName)
    {
        $container = Container::current();
        $container->registerClass(EmailProvider::class, $emailProviderClassName );
    }

    /**
     * Returns an instance of the default email provider
     *
     * @deprecated Use the dependency injection container instead
     * @return EmailProvider
     */
    public static function getDefaultEmailProvider()
    {
        return Container::instance(EmailProvider::class);
    }

    abstract public function sendEmail(Email $email);
}
