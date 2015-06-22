<?php

namespace Rhubarb\Crown\Tests\Logging;

use Rhubarb\Crown\Logging\Log;

class UnitTestLog extends Log
{
    public $entries = [];

    /**
     * The logger should implement this method to perform the actual log committal.
     *
     * @param string $message The text message to log
     * @param string $category
     * @param int $indent An indent level - if applicable this can be used to make logs more readable.
     * @param array $additionalData Any number of additional key value pairs which can be understood by specific
     *                                  logs (e.g. an API log might understand what AuthenticationToken means)
     * @return mixed
     */
    protected function WriteEntry($message, $indent, $category = "", $additionalData = [])
    {
        $this->entries[] = [$message, $category, $indent, $additionalData];
    }

    protected function ShouldLog($category)
    {
        global $logFilter;  // Yes, it's a global - it's a unit test. Get over it.

        if ($category == "ignore") {
            return false;
        }

        if (isset($logFilter) && $logFilter) {
            return false;
        }

        return parent::ShouldLog($category);
    }
}