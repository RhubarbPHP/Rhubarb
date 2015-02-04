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

use Rhubarb\Crown\Exceptions\EmailException;

class EmailAddress
{
    /**
     * @var string The email address portion e.g. john@hotmail.com
     */
    public $email;

    /**
     * @var string The name portion e.g. John Smithlock
     */
    public $name;

    public function __construct($email, $name = "")
    {
        if (strpos($email, "\"") !== false) {
            // The first parameter contains a double quote. This isn't valid in an email address so the contents
            // must be in a standard rfc format.
            if (preg_match('/"([^"]+)"\s+<([^>]+)>/', $email, $match)) {
                $this->email = $match[2];
                $this->name = $match[1];
            } else {
                throw new EmailException("The email " . $email . " is not a valid email address.");
            }
        } else {
            $this->email = $email;
        }

        if ($name != "") {
            $this->name = $name;
        }
    }

    public function getRfcFormat()
    {
        if ($this->name) {
            return '"' . $this->name . '" <' . $this->email . '>';
        }

        return $this->email;
    }

    function __toString()
    {
        return $this->getRfcFormat();
    }
} 