<?php

namespace Rhubarb\Crown\Logging;

use Monolog\Logger;

class MonologLog extends Log
{
    /**
     * @var Logger $logger
     */
    private $logger;

    public function __construct($logLevel, Logger $logger)
    {
        parent::__construct($logLevel);

        $this->logger = $logger;
    }


    /**
     * The logger should implement this method to perform the actual log committal.
     *
     * @param string $message The text message to log
     * @param string $category The category of log message
     * @param int $indent An indent level - if applicable this can be used to make logs more readable.
     * @param array $additionalData Any number of additional key value pairs which can be understood by specific
     *                                  logs (e.g. an API log might understand what AuthenticationToken means)
     * @return mixed
     */
    protected function writeEntry($message, $indent, $category = "", $additionalData = [])
    {
        $this->logger->addInfo($message);
    }
}