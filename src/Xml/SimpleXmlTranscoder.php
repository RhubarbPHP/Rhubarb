<?php

namespace Rhubarb\Crown\Xml;

class SimpleXmlTranscoder
{
    const XMLNS = 'rbrb';
    const TYPE_OBJECT = 'obj';
    const TYPE_ARRAY = 'arr';
    const TYPE_BOOL = 'bool';
    const TYPE_NUMERIC = 'num';
    const ATTR_TYPE = 'type';

    const BOOL_TRUE = 'true';
    const BOOL_FALSE = 'false';

    /**
     * @param $content
     * @return string
     */
    public static function encode($content)
    {
        $domDocument = new \DOMDocument();
        $domDocument->formatOutput = true;
        self::writeContents($content, $domDocument, $domDocument);
        return $domDocument->saveXML();
    }

    /**
     * @param $content
     * @param \DOMElement|\DOMDocument $element
     * @param \DOMDocument $document
     */
    private static function writeContents($content, $element, \DOMDocument $document)
    {
        if (is_object($content)) {
            $properties = get_object_vars($content);

            $object = self::createTypeElement($document, $element, self::TYPE_OBJECT);

            foreach ($properties as $property => $value) {
                $newElement = $document->createElement($property);
                $object->appendChild($newElement);
                self::writeContents($value, $newElement, $document);
            }
        } else {
            if (is_array($content)) {
                // determine if this is an associative array (in which case we want an object)
                $isAssoc = false;
                foreach ($content as $key => $value) {
                    if (!is_int($key)) {
                        $isAssoc = true;
                        break;
                    }
                }
                $nodeName = 'element';
                if ($isAssoc) {
                    $array = self::createTypeElement($document, $element, self::TYPE_OBJECT);
                } else {
                    $array = self::createTypeElement($document, $element, self::TYPE_ARRAY);
                    if (strtolower(substr($element->nodeName, -1)) === 's') {
                        $nodeName = substr($element->nodeName, 0, -1);
                    }
                }

                foreach ($content as $key => $value) {
                    if (!$isAssoc && is_int($key)) {
                        $arrayElement = $document->createElement($nodeName);
                    } else {
                        $arrayElement = $document->createElement($key);
                    }
                    $array->appendChild($arrayElement);
                    self::writeContents($value, $arrayElement, $document);
                }
            } else {
                if (is_numeric($content)) {
                    $element->setAttribute(self::XMLNS . ':' . self::ATTR_TYPE, self::TYPE_NUMERIC);
                    $text = $document->createTextNode($content);
                } elseif (is_bool($content)) {
                    $element->setAttribute(self::XMLNS . ':' . self::ATTR_TYPE, self::TYPE_BOOL);
                    $text = $document->createTextNode($content ? self::BOOL_TRUE : self::BOOL_FALSE);
                } else {
                    $text = $document->createTextNode($content);
                }
                $element->appendChild($text);
            }
        }
    }

    /**
     * @param \DOMDocument $document
     * @param \DOMElement|\DOMDocument|null $parentElement
     * @param string $type
     *
     * @return \DOMElement
     */
    private static function createTypeElement(\DOMDocument $document, $parentElement = null, $type)
    {
        if ($parentElement->parentNode === null) {
            $typeElement = $document->createElementNS(
                'https://github.com/RhubarbPHP/Rhubarb/blob/master/docs/simple-xml-transcoder.md',
                self::XMLNS . ':' . $type
            );
            $parentElement->appendChild($typeElement);
        } else {
            $parentElement->setAttribute(self::XMLNS . ':' . self::ATTR_TYPE, $type);
            $typeElement = $parentElement;
        }

        return $typeElement;
    }

    /**
     * @param string $content
     * @param bool $objectsToAssociativeArrays
     * @return mixed
     */
    public static function decode($content, $objectsToAssociativeArrays = false)
    {
        $result = null;
        $domDocument = new \DOMDocument();
        $domDocument->loadXML($content);
        return self::readNode($domDocument->firstChild, $objectsToAssociativeArrays);
    }

    /**
     * @param \DOMElement|\DomNode $node
     * @param $objectsToAssociativeArrays
     * @return array|bool|float|null|\stdClass|string
     */
    private static function readNode(\DOMElement $node, $objectsToAssociativeArrays)
    {
        $contents = null;
        $hasTypeAttr = $node->hasAttribute(self::XMLNS . ':' . self::ATTR_TYPE);
        if ($hasTypeAttr || $node->prefix === self::XMLNS) {
            switch ($hasTypeAttr ? $node->getAttribute(self::XMLNS . ':' . self::ATTR_TYPE) : $node->localName) {
                case self::TYPE_OBJECT:
                    if ($objectsToAssociativeArrays) {
                        $contents = [];
                    } else {
                        $contents = new \stdClass();
                    }
                    foreach ($node->childNodes as $childNode) {
                        if ($childNode instanceof \DOMElement) {
                            $propertyName = $childNode->localName;
                            if ($objectsToAssociativeArrays) {
                                $contents[$propertyName] = self::readNode($childNode, $objectsToAssociativeArrays);
                            } else {
                                $contents->$propertyName = self::readNode($childNode, $objectsToAssociativeArrays);
                            }
                        }
                    }
                    break;
                case self::TYPE_ARRAY:
                    $contents = [];
                    foreach ($node->childNodes as $childNode) {
                        if ($childNode instanceof \DOMElement) {
                            $contents[] = self::readNode($childNode, $objectsToAssociativeArrays);
                        }
                    }
                    break;
                case self::TYPE_NUMERIC:
                    if (strpos($node->nodeValue, '.')) {
                        $contents = (float)$node->nodeValue;
                    } else {
                        $contents = (int)$node->nodeValue;
                    }
                    break;
                case self::TYPE_BOOL:
                    $contents = $node->nodeValue === self::BOOL_TRUE;
                    break;
            }
        } else {
            $contents = $node->nodeValue;
        }

        return $contents;
    }
}
