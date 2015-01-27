<?php

namespace Gcd\Core\String;

use Gcd\Core\Modelling\UnitTesting\Example;
use Gcd\Core\UnitTesting\CoreTestCase;

class TemplateTest extends CoreTestCase
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
