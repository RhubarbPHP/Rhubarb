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

namespace Rhubarb\Crown\Tests\unit\String;

use Rhubarb\Crown\String\StringTools;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class StringToolsTest extends RhubarbTestCase
{
    public function testStringsCanBeWordified()
    {
        $this->assertEquals("This Is My Full Name", StringTools::wordifyStringByUpperCase("ThisIsMyFullName"));
    }

    public function testSingularisation()
    {
        $words = [
            "churches" => "church",
            "goats" => "goat",
            "ships" => "ship",
            "companies" => "company"
        ];

        foreach ($words as $plural => $singular) {
            $this->assertEquals($singular, StringTools::makeSingular($plural));
        }
    }

    public function testPluralisation()
    {
        $words = [
            "church" => "churches",
            "goat" => "goats",
            "ship" => "ships",
            "company" => "companies",
            "fox" => "foxes",
            "grass" => "grasses"
        ];

        foreach ($words as $singular => $plural) {
            $this->assertEquals($plural, StringTools::makePlural($singular));
        }
    }

    public function testExplodingString()
    {
        $string = "/my/string/with/slashes/";

        $result = StringTools::explodeIgnoringBlanks("/", $string);

        $this->assertEquals(4, count($result));
    }

    public function testExplodingStringWithNoTrailingGlue()
    {
        $string = "/my/string/with/slashes";

        $result = StringTools::explodeIgnoringBlanks("/", $string);

        $this->assertEquals(4, count($result));
    }

    public function testExplodingStringWithNoLeadingGlue()
    {
        $string = "my/string/with/slashes/";

        $result = StringTools::explodeIgnoringBlanks("/", $string);

        $this->assertEquals(4, count($result));
    }

    public function testNoEmptyStringsReturned()
    {
        $string = "my/string//slashes/";

        $result = StringTools::explodeIgnoringBlanks("/", $string);

        $this->assertEquals(3, count($result));
    }

    public function testExplodeWithLimit()
    {
        $string = "my/string/with/slashes/and/stuff/";

        $result = StringTools::explodeIgnoringBlanks("/", $string, 2);

        $this->assertEquals(2, count($result));
    }
}
