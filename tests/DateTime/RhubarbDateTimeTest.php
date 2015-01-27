<?php

namespace Rhubarb\Crown\DateTime;

use Rhubarb\Crown\UnitTesting\RhubarbTestCase;

class RhubarbDateTimeTest extends RhubarbTestCase
{
	public function testDatesInitaliseProperly()
	{
		$now = time();

		$date = new RhubarbDateTime( $now );
		$this->assertEquals( date( "Y-m-d", $now ), $date->format( "Y-m-d" ) );

		$date = new RhubarbDateTime( "2013-09-03" );
		$this->assertEquals( "2013-09-03", $date->format( "Y-m-d" ) );

		$date = new RhubarbDateTime( new \DateTime( "2013-09-03" ) );
		$this->assertEquals( "2013-09-03", $date->format( "Y-m-d" ) );

		$date = new RhubarbDateTime( new \DateTime( "0001-01-01" ) );
		$this->assertEquals( "0001-01-01", $date->format( "Y-m-d" ) );

		$date = new RhubarbDateTime( "now" );
		$this->assertEquals( date( "Y-m-d", $now ), $date->format( "Y-m-d" ) );

		$date = new RhubarbDateTime( "tomorrow" );
		$this->assertEquals( date( "Y-m-d", $now + 86400 ), $date->format( "Y-m-d" ) );
		
	}

	public function testInvalidDates()
	{
		$date = new RhubarbDateTime();

		$this->assertFalse( $date->IsValidDateTime() );

		$date = new RhubarbDateTime( "now" );

		$this->assertTrue( $date->IsValidDateTime() );

		$date = new RhubarbDateTime( "czcvz-23-122" );

		$this->assertFalse( $date->IsValidDateTime() );

		$date = new RhubarbDateTime( "0000-00-00" );

		$this->assertFalse( $date->IsValidDateTime() );

		$date = new RhubarbDateTime( "0000-00-00 00:00:00" );

		$this->assertFalse( $date->IsValidDateTime() );

		ob_start();

		print $date;

		$string = ob_get_clean();

		$this->assertEquals( "", $string );

		$this->assertEquals( "", $date->format( "Y-m-d" ) );
	}

	public function testDatePrints()
	{
		$date = new RhubarbDateTime( "2013-09-03" );

		ob_start();

		print $date;

		$dateString = ob_get_clean();

		$this->assertEquals( "03-Sep-2013", $dateString );

		$date = new RhubarbDateTime( "abcdefg" );

		ob_start();

		print $date;

		$dateString = ob_get_clean();

		$this->assertEquals( "", $dateString, "Invalid dates should print an empty string." );
	}

	public function testJsonEncode()
	{
		$date = new RhubarbDateTime( "2013-09-03" );
		$encoded = json_encode( $date );

		$this->assertEquals( '"'.$date->format( \DateTime::ISO8601 ).'"', $encoded );

		$date = new RhubarbDateTime( str_replace( '"', "", $encoded ) );

		$this->assertEquals( "2013-09-03", $date->format( "Y-m-d" ) );
	}

	public function testPreviousMonday()
	{
		$refDate = new RhubarbDate( "2014-03-31" );

		$newDate = RhubarbDateTime::PreviousMonday( $refDate );

		$this->assertEquals( $refDate->format( "Ymd" ), $newDate->format( "Ymd" ) );

		$refDate = new RhubarbDate( "2014-04-02" );

		$newDate = RhubarbDateTime::PreviousMonday( $refDate );

		$this->assertEquals( "20140331", $newDate->format( "Ymd" ) );

		$refDate = new RhubarbDate( "2014-03-30" );

		$newDate = RhubarbDateTime::PreviousMonday( $refDate );

		$this->assertEquals( "20140324", $newDate->format( "Ymd" ) );
	}
}
