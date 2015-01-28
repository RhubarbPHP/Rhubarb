<?php

namespace Rhubarb\Crown\Tests\String;

use Rhubarb\Crown\String\Template;
use Rhubarb\Crown\Tests\RhubarbTestCase;

class TemplateTest extends RhubarbTestCase
{
    public function testTemplateParsing()
    {
        $plainTextTemplate = "Nothing in here to parse";

        $this->assertEquals($plainTextTemplate, Template::parseTemplate($plainTextTemplate, array()));

        $template = "Ah something to process! {Forename}";

        $this->assertEquals("Ah something to process! Andrew",
            Template::parseTemplate($template, array("Forename" => "Andrew")));
    }
}
