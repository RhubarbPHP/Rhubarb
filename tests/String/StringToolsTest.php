<?php

namespace Rhubarb\Crown\Tests\String;

use Rhubarb\Crown\String\StringTools;
use Rhubarb\Crown\Tests\RhubarbTestCase;

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
}
