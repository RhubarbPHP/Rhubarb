<?php

namespace Rhubarb\Crown\Tests\unit\Xml;

use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Crown\Xml\SimpleXmlTranscoder;

class SimpleXmlTranscoderTest extends RhubarbTestCase
{
    public function testMatchesJSONResult()
    {
        $object = new \stdClass();
        $object->key = "Value";
        $object->list = [
            "a",
            "b",
            "c",
            -19.9,
            -0.9,
            1.0,
            1.01,
            1,
            2,
            3,
            true,
            false,
        ];

        $l1 = new \stdClass();
        $l1->property = "propertyValue";
        $l1->property2 = "propertyValue2";
        $object->objectList = [
            $l1,
        ];
        $object->object = $l1;

        $object->assocArray = [
            "oneThing" => "leads to another",
        ];

        $object->array2 = [
            [
                'abc',
            ],
        ];

        $inputs = [
            $object,
            ['a', 'b', 'c', 1, 2, 3, new \stdClass(), true, false],
        ];

        foreach ($inputs as $input) {
            // check that encoding and decoding results in the exact same output as the json methods
            self::assertEquals(
                var_export(json_decode(json_encode($input)), true),
                var_export(SimpleXmlTranscoder::decode(SimpleXmlTranscoder::encode($input)), true)
            );

            // check that associative array decoding works creates the same output as json_decode with the assoc flag
            self::assertEquals(
                var_export(json_decode(json_encode($input), true), true),
                var_export(SimpleXmlTranscoder::decode(SimpleXmlTranscoder::encode($input), true), true)
            );
        }
    }
}
