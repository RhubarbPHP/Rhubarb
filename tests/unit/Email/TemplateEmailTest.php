<?php

namespace Rhubarb\Crown\Tests\unit\Email;

use Rhubarb\Crown\Email\TemplateEmail;
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

class UnitTestingTemplateEmail extends TemplateEmail
{
    protected function getTextTemplateBody()
    {
        return "Your name is {Name}";
    }

    protected function getHtmlTemplateBody()
    {
        return "Your age is {Age}";
    }

    protected function getSubjectTemplate()
    {
        return "Your hair is {HairColour}";
    }
}

class FancyUnitTestingTemplateEmail extends UnitTestingTemplateEmail
{
    protected function getHtmlLayout()
    {
        return "<div>{Content}</div>";
    }

    protected function getTextLayout()
    {
        return "abc{Content}def";
    }
}
