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

namespace Rhubarb\Crown\Sendables\Email;

require_once __DIR__ . '/../../Settings.php';

use Rhubarb\Crown\Application;
use Rhubarb\Crown\PhpContext;
use Rhubarb\Crown\Settings;

/**
 * Container for some default properties for sending emails.
 *
 * @property EmailRecipient $DefaultSender The default sender to use for all emails (unless set explicitly in the email classes)
 * @property EmailRecipient|bool $OnlyRecipient If you wish to prevent a development setup from emailing real customer addresses, set this to a test recipient address
 */
class EmailSettings extends Settings
{
    public $onlyRecipient = false;
    public $onlyCcRecipient = false;
    public $onlyBccRecipient = false;

    public $defaultSender;

    protected function initialiseDefaultValues()
    {
        parent::initialiseDefaultValues();

        $request = Application::current()->request();
        $host = $request->server("SERVER_NAME");

        $this->defaultSender = new EmailRecipient("donotreply@" . $host . ".com");
    }
}
