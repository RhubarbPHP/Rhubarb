<?php

namespace Rhubarb\Crown\Tests\unit\String;

use Rhubarb\Crown\String\Template;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class TemplateTest extends RhubarbTestCase
{
    public function testTemplateParsing()
    {
        $plainTextTemplate = "Nothing in here to parse";

        $this->assertEquals($plainTextTemplate, Template::parseTemplate($plainTextTemplate, []));

        $template = "Ah something to process! {Forename}";

        $this->assertEquals(
            "Ah something to process! Andrew",
            Template::parseTemplate($template, ["Forename" => "Andrew"])
        );
    }
}
