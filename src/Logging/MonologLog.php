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


    protected function writeEntry($level, $message, $indent, $category = "", $additionalData = [])
    {
        $message = str_pad($category, 16, " ", STR_PAD_RIGHT )."\t". str_repeat("  ", $indent).$message;

        switch($level)
        {
            case Log::BULK_DATA_LEVEL:
                $this->logger->addDebug($message, $additionalData);
                break;
            case Log::DEBUG_LEVEL:
                $this->logger->addDebug($message, $additionalData);
                break;
            case Log::ERROR_LEVEL:
                $this->logger->addError($message, $additionalData);
                break;
            case Log::PERFORMANCE_LEVEL:
                $this->logger->addNotice($message, $additionalData);
                break;
            case Log::WARNING_LEVEL:
                $this->logger->addWarning($message, $additionalData);
                break;
            case Log::REPOSITORY_LEVEL:
                $this->logger->addNotice($message, $additionalData);
                break;
            default:
                $this->logger->addNotice($message, $additionalData);
        }
    }
}