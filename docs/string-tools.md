StringTools
============

StringTools is a static utility class for manipulating strings.

contains($haystack, $needle, $caseSensitive = true)
: Faster than using preg_match, this will use strpos or stripos depending on the third argument

endsWith($haystack, $needle, $caseSensitive = true)
: Faster than using preg_match, this will use strpos or stripos depending on the third argument

getCharsAfterMatch($string, $search, $firstOccurrence = false, $maxChars = null, $caseSensitive = true, $includeSearch = false, $returnIfNoMatch = false)
: Returns all characters in $string after the first (or last, depending on $firstOccurrence switch) match of $search

getNamespaceFromClass($fullyQualifiedClassName)
: Extracts and returns the namespace portion of a class name

getShortClassNameFromNamespace($fullyQualifiedClassName)
: Returns the last part of the fully namspaced class name.

implodeIgnoringBlanks($glue, $array, $itemCallback = null, $keysToInclude = null)
: Implodes an array to a string with $glue but ignores blank or empty values. Optionally $itemCallback can specify
  a callback function to transform elements during the implosion.

listToSentence($wordList)
: Turns an array of strings into a comma separated list with the word " and " between the final two

makePlural($singular)
: Turn a singular English noun to its plural equivalent.

makePluralWithQuantity($singular, $quantity, $includeCount = false, $decimalPlaces = 0)
: Return a plural form of a singluar English noun if $quantity is greater than 1. Optionally can prefix the
  plural word with the quantity itself.

makeSingular($plural)
: Turn a plural English noun to its singular equivalent.

parseTemplateString($string, $data)
: Parses a template string $string for placeholders enclosed in curly braces {} and replaces the placeholders with
  values from the $data associative array.

removeCharsFromEnd
:

replaceFirst

startsWith($haystack, $needle, $caseSensitive = true)
: Faster than using preg_match, this will use strpos or stripos depending on the third argument

wordifyStringByUpperCase