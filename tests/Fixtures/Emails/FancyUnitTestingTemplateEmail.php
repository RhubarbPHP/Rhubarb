<?php
/**
 * Created by PhpStorm.
 * User: acuthbert
 * Date: 22/03/16
 * Time: 13:27
 */

namespace Rhubarb\Crown\Tests\Fixtures\Emails;


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