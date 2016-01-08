<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Crown\Tests\unit\Xml;

use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Crown\Xml\Node;
use Rhubarb\Crown\Xml\NodeStrategyCollation;
use Rhubarb\Crown\Xml\NodeStrategyCollationDictionary;
use Rhubarb\Crown\Xml\NodeStrategyTraversal;
use Rhubarb\Crown\Xml\XmlParser;

class XmlParserTest extends RhubarbTestCase
{
    protected function setUp()
    {
        parent::setUp();

        // Create a text file to stream.

        file_put_contents("cache/unit-test-xml-stream.xml", '<?xml version="1.0" encoding="ISO-8859-1"?>
<meals>
	<breakfast>
		<food>Sausage</food>
		<food>Eggs</food>
		<food>Eggs</food>
	</breakfast>
	<lunch>
		<food warmth="cold">Yoghurt</food>
		<drinks ordered="yes">
			<drink>Coke</drink>
			<drink type="decaff">Tea</drink>
		</drinks>
	</lunch>
	<dinner>
		<!-- Test CDATA while were at it -->
		<dessert><![CDATA[Apple <b>Pie</b>]]>s<!-- Test of comments --></dessert>
	</dinner>
	<mints colour="dark" />
	<!-- The desert below is not in a meal to test restricting a scan to a parent -->
	<dessert>Rhubarb Crumble</dessert>
	<dictionary height="tall">
		<name>George</name>
		<children big="deal">
			<joe>son</joe>
			<mary></mary>
			<john></john>
		</children>
		<interest>Fishing</interest>
		<interest>Golf</interest>
		<interest>
			<complexInterest>Sewing</complexInterest>
			<complexInterest>Knitting</complexInterest>
		</interest>
		<word>
			<name>a</name>
		</word>
		<word>
			<name>b</name>
		</word>
		<word>
			<name>c</name>
		</word>
	</dictionary>
</meals>
');
    }

    public function testStreamReadsElements()
    {
        $hit = false;

        $parser = new XmlParser("cache/unit-test-xml-stream.xml");
        $parser->addNodeHandler("breakfast", new NodeStrategyCollation(
            function (Node $node) use (&$hit) {
                $hit = true;
            }
        ));

        $parser->Parse();

        $this->assertTrue($hit);

        $parser = new XmlParser("cache/unit-test-xml-stream.xml");
        $parser->addNodeHandler("breakfast", new NodeStrategyCollation(
            function (Node $node) use (&$hit) {
                $hit = $node;
            }
        ));

        $parser->Parse();

        $this->assertEquals("breakfast", $hit->name);
        $this->assertEmpty($hit->attributes);
        $this->assertEquals(1, $hit->depth);
        $this->assertCount(3, $hit->children);

        $this->assertEquals("Sausage", $hit->children[0]->text);

        $parser = new XmlParser("cache/unit-test-xml-stream.xml");
        $parser->addNodeHandler("lunch", new NodeStrategyCollation(
            function (Node $node) use (&$hit) {
                $hit = $node;
            }
        ));

        $parser->Parse();

        $this->assertEquals("cold", $hit->children[0]->attributes["warmth"]);
        $this->assertEquals("decaff", $hit->children[1]->children[1]->attributes["type"]);

        $hit = false;

        $dinnerSniffer = new NodeStrategyTraversal();
        $dinnerSniffer->addNodeHandler("dessert", new NodeStrategyCollation(function (Node $node) use (&$hit) {
            $hit = $node;
        }));

        $parser = new XmlParser("cache/unit-test-xml-stream.xml");
        $parser->addNodeHandler("dinner", $dinnerSniffer);

        $parser->Parse();

        $this->assertEquals("Apple <b>Pie</b>s", $hit->text);

        $parser = new XmlParser("cache/unit-test-xml-stream.xml");
        $parser->addNodeHandler("mints", new NodeStrategyCollation(function (Node $node) use (&$hit) {
            $hit = $node;
        }));

        $parser->Parse();

        $this->assertEquals("dark", $hit->attributes["colour"], "Self closed tags don't work no more");
    }

    public function testParseOne()
    {
        $hit = false;

        $parser = new XmlParser("cache/unit-test-xml-stream.xml");
        $parser->addNodeHandler("dessert", new NodeStrategyCollation(function (Node $node) use (&$hit) {
            $hit = $node;
        }));

        $result = $parser->parseOne();

        $this->assertTrue($result);
        $this->assertEquals("Apple <b>Pie</b>s", $hit->text);

        $result = $parser->parseOne();
        $this->assertTrue($result);

        $result = $parser->parseOne();
        $this->assertFalse($result);
    }

    public function testDictionaryStrategy()
    {
        $hit = false;

        $parser = new XmlParser("cache/unit-test-xml-stream.xml");
        $parser->addNodeHandler("dictionary", new NodeStrategyCollationDictionary(function ($node) use (&$hit) {
            $hit = $node;
        }));

        $parser->Parse();

        $this->assertEquals("George", $hit["name"]);
        $this->assertEquals("tall", $hit["height"]);
        $this->assertCount(4, $hit["children"]);
        $this->assertEquals("son", $hit["children"]["joe"]);
        $this->assertEquals("deal", $hit["children"]["big"]);
        $this->assertCount(3, $hit["interest"]);
        $this->assertEquals("Fishing", $hit["interest"][0]);
        $this->assertCount(2, $hit["interest"][2]["complexInterest"]);
        $this->assertEquals("Knitting", $hit["interest"][2]["complexInterest"][1]);
        $this->assertCount(3, $hit["word"]);
        $this->assertEquals("a", $hit["word"][0]["name"]);
    }
}