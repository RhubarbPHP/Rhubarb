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

namespace Rhubarb\Crown\Tests\Sendables\Email;

use Rhubarb\Crown\Sendables\Email\TemplateEmail;
use Rhubarb\Crown\Tests\Fixtures\Emails\FancyUnitTestingTemplateEmail;
use Rhubarb\Crown\Tests\Fixtures\Emails\UnitTestingTemplateEmail;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class TemplateEmailTest extends RhubarbTestCase
{
    public function testTemplateEmailWorks()
    {
        $email = new UnitTestingTemplateEmail(["Name" => "Fairbanks", "Age" => "21++", "HairColour" => "brown"]);

        $this->assertEquals("Your name is Fairbanks", $email->getText());
        $this->assertEquals("Your age is 21++", $email->getHtml());
        $this->assertEquals("Your hair is brown", $email->getSubject());

        $email = new FancyUnitTestingTemplateEmail(["Name" => "Fairbanks", "Age" => "21++", "HairColour" => "brown"]);

        $this->assertEquals("<div>Your age is 21++</div>", $email->getHtml(), "Templated emails using layouts aren't using the html layout");
        $this->assertEquals("abcYour name is Fairbanksdef", $email->getText(), "Templated emails using layouts aren't using the text layout");
    }
}

