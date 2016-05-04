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

namespace Rhubarb\Crown\Tests\unit\Email;

use Rhubarb\Crown\Sendables\Email\EmailRecipient;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class EmailAddressTest extends RhubarbTestCase
{
    public function testCreation()
    {
        $emailAddress = new EmailRecipient("acuthbert@gcdtech.com");

        $this->assertEquals("acuthbert@gcdtech.com", $emailAddress->email);

        $internetFormat = $emailAddress->getRfcFormat();

        $this->assertEquals("acuthbert@gcdtech.com", $internetFormat);

        $emailAddress = new EmailRecipient("acuthbert@gcdtech.com", "Andrew Cuthbert");

        $this->assertEquals("Andrew Cuthbert", $emailAddress->name);

        $internetFormat = $emailAddress->getRfcFormat();

        $this->assertEquals("\"Andrew Cuthbert\" <acuthbert@gcdtech.com>", $internetFormat);

        $emailAddress = new EmailRecipient("\"Andrew Cuthbert\" <acuthbert@gcdtech.com>");
        $this->assertEquals("acuthbert@gcdtech.com", $emailAddress->email);
        $this->assertEquals("Andrew Cuthbert", $emailAddress->name);

        $emailAddress = new EmailRecipient("\"Andrew Cuthbert\" <acuthbert@gcdtech.com>", "Mary");

        $this->assertEquals("Mary", $emailAddress->name);
    }
}
