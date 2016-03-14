Date and Time Processing
====================

Rhubarb provides three classes to make date and time processing easier:

RhubarbDateTime
:   Extends the PHP DateTime class

RhubarbDate
:   Extends RhubarbDateTime and provides time agnostic handling of dates only

RhubarbTime
:   Extends RhubarbDateTime and provides date agnostic handling of times only

RhubarbDateInterval
:   Extends DateInterval and provides additional properties to get the total number of hours, minutes, seconds etc.

## RhubarbDateTime

All of the following are valid ways to construct the same date time:

~~~ php
// Assume today is 2013-09-18
$date = new RhubarbDateTime( "today" );
$date = new RhubarbDateTime( "2013-09-18" );
$date = new RhubarbDateTime( 1379510324 );
~~~

Valid date stanzas are those built in to PHP as handled by
[strtotime](http://php.net/manual/en/datetime.formats.relative.php) for example.

RhubarbDateTime also provides a simple way to recognise invalid dates.

~~~ php
$date = new RhubarbDateTime( "thisisnotadatetime" );
$valid = $date->isValidDateTime();  // False
~~~

Just like DateTime dates can be compared with ==, &lt; and &gt;

## RhubarbTime

Construct a RhubarbTime by passing a simple time string:

~~~ php
$time = new RhubarbTime( "12:00" );
$time = new RhubarbTime( "12:22:43" );
~~~

And you can also pass in a RhubarbDateTime to extract its time.

~~~ php
$startDateTime = new RhubarbDateTime( "2013-09-18 14:15:12" );
$time = new RhubarbTime( $startDateTime );
~~~

Any two times can then can be compared with ==, &lt; and &gt;

~~~ php
$startDateTime = new RhubarbDateTime( "2013-09-18 09:15:12" );
$endDateTime = new RhubarbDateTime( "1920-10-31 15:15:12" );

$startTime = new RhubarbTime( $startDateTime );
$endTime = new RhubarbTime( $endDateTime );

if ( $endTime > $startTime )
{
    // Phew, nature is still working correctly...
}
~~~

## RhubarbDateInterval

When two RhubarbDateTime or RhubarbTime objects are subtracted using `diff()` a RhubarbDateInterval object is returned. This
extends the standard PHP DateInterval object by adding totals.

~~~ php
$startTime = new RhubarbTime( "09:00:00" );
$endTime = new RhubarbTime( "10:30:15" );

$difference = $endDateTime->diff( $startDateTime );

print $difference->totalWeeks;      // 0.008953373015873
print $difference->totalDays;       // 0.0626736111111111
print $difference->totalHours;      // 1.5042
print $difference->totalMinutes;    // 90.25
print $difference->totalSeconds;    // 5415

$startDate = new RhubarbDateTime( "2013-01-01" );
$endDate = new RhubarbDateTime( "2014-06-01" );

print $difference->totalMonths;      // 17
print $difference->totalYears;       // 1.4167
~~~

## Date handling in Modelling

The DateTime and Time column types use RhubarbDateTime classes internally. If you use these column types in
your schema you can be assured that the values returned from models will already be in RhubarbDateTime objects
letting your code do clever things with out much effort.

~~~ php
if ( $contact->DateOfBirth > new RhubarbDateTime( "-10 years" ) )
{
    print "Congratulations - you're at least 10 years old.";
}
~~~
