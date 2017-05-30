<?php

namespace Rhubarb\Crown\Exceptions;

class ErrorWithTraceException extends RhubarbException
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
        parent::__construct($message);

        $this->message = $message;
        $this->code = $code;
        $this->severity = $severity;
        $this->file = $filename;
        $this->line = $lineno;
        $this->backtrace = $backtrace;
    }

    public function __toString()
    {
        $class = get_class($this);

        return <<<ERROR
exception '$class' with message '$this->message' in $this->file:$this->line
$this->backtrace
ERROR;
    }
}
