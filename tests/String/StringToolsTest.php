<?php

namespace Gcd\Core\String;

use Gcd\Core\UnitTesting\CoreTestCase;

class StringToolsTest extends CoreTestCase
{
	public function testStringsCanBeWordified()
	{
		$this->assertEquals( "This Is My Full Name", StringTools::WordifyStringByUpperCase( "ThisIsMyFullName" ) );
	}

	public function testSingularisation()
	{
		$words = array(
			"churches" => "church",
			"goats" => "goat",
			"ships" => "ship",
			"companies" => "company"
		);

		foreach( $words as $plural => $singular )
		{
			$this->assertEquals( $singular, StringTools::MakeSingular( $plural ) );
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

		foreach( $words as $singular => $plural )
		{
			$this->assertEquals( $plural, StringTools::MakePlural( $singular ) );
		}
	}
}
