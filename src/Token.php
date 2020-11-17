<?php

namespace YaFou\Visuel;

class Token
{
    public const TEXT = 'text';
    public const PRINT_START = 'print_start';
    public const PRINT_END = 'print_end';
    public const BLOCK = 'block';
    public const ARGUMENTS_START = 'arguments_start';
    public const ARGUMENTS_END = 'arguments_end';
    public const STATEMENT = 'statement';

    /**
     * @var string
     */
    private $type;
    /**
     * @var string|null
     */
    private $value;

    public function __construct(string $type, string $value = null)
    {
        $this->type = $type;
        $this->value = $value;
    }
}
