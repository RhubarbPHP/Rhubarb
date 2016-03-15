Record Streams
==============

Streaming is a concept familiar to most programmers with desktop or mobile programming experience. Simply put
streaming allows reading or writing large volumes of data in chunks in order to be possible and efficient on
a machine with finite memory.

Rhubarb includes a stream pattern which takes this concept and applies it to structured records of data.

A `RecordStream` object has the following methods:

readNextItem()
:   Returns the next item from the stream or false if the end of the stream has been reached. The item data
    will be an associative array of key value pairs.

appendItem($item)
:   Appends the supplied item to the stream if the stream supports writing.

appendStream(RecordStream $sourceStream)
:   Appends all the items from the source stream

If your task involves extracting multiple items from a file or network resource it's recommended to build a
stream class to do this. Once made your stream can be connected easily with other streams to push and pull the
items from and to different sources.

## Rhubarb Stream classes

### CsvStream

Rhubarb implements its own CSV file reader in the `CsvStream` class. This implementation is more reliable that
fgetcsv at handling carriage returns inside enclosures and escaping of enclosures.

In addition to the standard `RecordStream` methods `CsvStream` also has:

getHeaders()
:   Reads enough of the CSV file to return an array of the headers from the CSV file (first line).

setHeaders($headers)
:   If writing to a new CSV file you can supply a list of the headers you want in the file. If you don't specify
    the headers by calling `setHeaders` all of the first item's keys will be used instead.

close()
:   Call to close the open file handle on the CSV file. The file will be closed naturally when the script ends so
    calling this isn't mandatory unless you want to open the file for additional operations.

To use the CsvStream simple pass the path to the constructor and then configure its settings via public
properties:

``` php
$stream = new CsvStream(__DIR__."/currencies.csv");
$stream->enclosure = '"';
$stream->delimiter = "\t";  // Tab delimited file
```

The possible public properties are:

$enclosure
:   The character used to enclose string values.

$delimiter
:   The character used to separate cells

$escapeCharacter
:   The character used to escape the enclosure character itself when already within an enclosure. The default option
    (blank) expects the enclosure itself to be doubled (e.g. two "" characters to denote a single ") however
    CSV files from some software might use a backslash.

### XmlStream

The Rhubarb XmlStream uses the xml reading classes of Rhubarb to target an element and stream the immediate
children as item data. For example given the following xml:

``` xml
<?xml version="1.0" encoding="ISO-8859-1"?>
<meals>
	<meal>
		<name>Breakfast</name>
		<calories>100</calories>
	</meal>
	<meal>
		<name>Dinner</name>
		<calories>200</calories>
	</meal>
	<meal>
		<name>Lunch</name>
		<calories>300</calories>
	</meal>
</meals>
```

The following stream would read the `meal` nodes:

``` php
$stream = new XmlStream("meal", "example.xml");
while($item = $stream->readNextItem()){
    print $meal['name'];
}
```

`XmlStream` objects are read only. Trying to append items will throw an `ImplementationException`.