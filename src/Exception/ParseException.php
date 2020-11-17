<?php

namespace YaFou\Visuel\Exception;

use Exception;

class ParseException extends Exception
{
    public function __construct(string $message, string $name, int $line, int $column)
    {
        parent::__construct(sprintf(
            'Syntax exception at "%s" line %d column %d: %s',
            $name,
            $line,
            $column,
            $message
        ));
    }
}
