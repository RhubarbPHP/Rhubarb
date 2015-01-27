<?php

namespace Rhubarb\Crown\String;

use Rhubarb\Crown\Modelling\UnitTesting\Example;
use Rhubarb\Crown\UnitTesting\RhubarbTestCase;

class TemplateTest extends RhubarbTestCase
{
	public function testTemplateParsing()
	{
		$plainTextTemplate = "Nothing in here to parse";

		$this->assertEquals( $plainTextTemplate, Template::ParseTemplate( $plainTextTemplate, array() ) );

		$template = "Ah something to process! {Forename}";

		$this->assertEquals( "Ah something to process! Andrew", Template::ParseTemplate( $template, array( "Forename" => "Andrew" ) ) );

		$susan = new Example();
		$susan->Forename = "Susan";

		$this->assertEquals( "Ah something to process! Susan", Template::ParseTemplate( $template, $susan ) );
	}
}
