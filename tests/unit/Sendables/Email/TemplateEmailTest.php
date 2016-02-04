<?php

namespace Rhubarb\Crown\Tests\unit\Sendables\Email;

use Rhubarb\Crown\Sendables\Email\TemplateEmail;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class TemplateEmailTest extends RhubarbTestCase
{
    public function testTemplateEmailWorks()
    {
        $email = new UnitTestingTemplateEmail(["Name" => "Fairbanks", "Age" => "21++", "HairColour" => "brown"]);

        $this->assertEquals("Your name is Fairbanks", $email->GetText());
        $this->assertEquals("Your age is 21++", $email->GetHtml());
        $this->assertEquals("Your hair is brown", $email->GetSubject());

        $email = new FancyUnitTestingTemplateEmail(["Name" => "Fairbanks", "Age" => "21++", "HairColour" => "brown"]);

        $this->assertEquals("<div>Your age is 21++</div>", $email->GetHtml(), "Templated emails using layouts aren't using the html layout");
        $this->assertEquals("abcYour name is Fairbanksdef", $email->GetText(), "Templated emails using layouts aren't using the text layout");
    }
}

class UnitTestingTemplateEmail extends TemplateEmail
{
    protected function GetTextTemplateBody()
    {
        return "Your name is {Name}";
    }

    protected function GetHtmlTemplateBody()
    {
        return "Your age is {Age}";
    }

    protected function GetSubjectTemplate()
    {
        return "Your hair is {HairColour}";
    }
}

class FancyUnitTestingTemplateEmail extends UnitTestingTemplateEmail
{
    protected function GetHtmlLayout()
    {
        return "<div>{Content}</div>";
    }

    protected function GetTextLayout()
    {
        return "abc{Content}def";
    }
}
