Simple XML Transcoder
============

The simple xml transcoder is designed to be an XML equivalent of php's built in `json_encode` / `json_decode` functions.

## Usage

This helper class provides two methods. Each method should behave exactly like it's json equivalent, 
but with XML input/output strings. 

### Encode

`\Rhubarb\Crown\Xml\SimpleXmlTranscoder::encode($mixed)`

### Decode

`\Rhubarb\Crown\Xml\SimpleXmlTranscoder::decode($mixed, $objectsToAssociativeArrays = false)`

## XML Vocabulary

### XMLNS

Any node names or attributes which the decoder relies on will be use the `rbrb` namespace.

### Type Attribute

Denotes how the contents of a node should be decoded. 
Types can be set either using the `type` attribute on a named node,
or as the actual node name if the node has no other meaningful name (eg the root node). 

Supported types:
* `obj` denotes an object. Expect child nodes to be named as the object's property names. 
Node Values are the property values.
* `arr` denotes an array. Each child node is an entry in the array. Child node names are inconsequential,
but will try to take the single form of a plural array node name when using the `encode` method. 
* `num` the data type of this node's value should be treated as a number
* `bool` the data type of this node's value should be treated as a boolean. Possible value: `true`/`false`
