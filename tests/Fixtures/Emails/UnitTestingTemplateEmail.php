<?php
namespace Rhubarb\Crown\Tests\Fixtures\Emails;

use Rhubarb\Crown\Sendables\Email\TemplateEmail;

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