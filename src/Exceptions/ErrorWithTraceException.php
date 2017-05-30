<?php

namespace Rhubarb\Crown\Exceptions;

class ErrorWithTraceException extends \ErrorException
{
    public $backtrace;

    /**
     * @param string $message
     * @param int $code
     * @param int $severity
     * @param string $filename
     * @param int $lineno
     * @param string $backtrace
     */
    public function __construct($message = "", $code = 0, $severity = 1, $filename = __FILE__, $lineno = __LINE__, $backtrace)
    {
        parent::__construct($message, $code, $severity, $filename, $lineno);

        $this->backtrace = $backtrace;
    }

    public function __toString()
    {
        $class = get_class($this);

        return <<<ERROR
exception '$class' with message '$this->getMessage()' in $this->getFile():$this->getLine()
$this->backtrace
ERROR;
    }
}
