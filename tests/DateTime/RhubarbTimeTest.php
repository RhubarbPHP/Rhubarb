<?php


namespace Rhubarb\Crown\DateTime;

use Rhubarb\Crown\UnitTesting\RhubarbTestCase;


class CoreTimeTest extends RhubarbTestCase
{
	public function testTimeAlwaysHasSameDay()
	{
		$time = new CoreTime( "10:00:00" );

		$this->assertEquals( "2000-01-01 10:00:00", $time->format( "Y-m-d H:i:s" ) );

		$time = new CoreTime( new RhubarbDateTime( "10:00" ) );

		$this->assertEquals( "2000-01-01 10:00:00", $time->format( "Y-m-d H:i:s" ) );
	}

	public function testTimeAlwaysHasSameDayEvenIfDeveloperSpecifiesDate()
	{
		$time = new CoreTime( "10:00:00" );

		$this->assertEquals( "2000-01-01 10:00:00", $time->format( "Y-m-d H:i:s" ) );

		$time = new CoreTime( new RhubarbDateTime( "2010-01-01 10:00" ) );

		$this->assertEquals( "2000-01-01 10:00:00", $time->format( "Y-m-d H:i:s" ) );
	}

	public function testTimeAlwaysHasSameDayEvenIfDeveloperSpecifiesDateAfterConstructor()
	{
		$time = new CoreTime( "10:00:00" );

		$this->assertEquals( "2000-01-01 10:00:00", $time->format( "Y-m-d H:i:s" ) );

		$time = new CoreTime( new RhubarbDateTime( "2010-01-01 10:00" ) );
		$time->setDate( 2010,02,05 );

		$this->assertEquals( "2000-01-01 10:00:00", $time->format( "Y-m-d H:i:s" ) );
	}

    public function testTimesGetCompared()
    {
        $time1 = new CoreTime( "17:00:00" );
        $time2 = new CoreTime( "18:00:00" );

        $this->assertNotEquals( (string) $time1, (string) $time2 );
    }
}
