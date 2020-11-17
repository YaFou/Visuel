<?php

namespace YaFou\Visuel;

class Source
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $code;
    /**
     * @var array
     */
    private $context;

    public function __construct(string $name, string $code, array $context = [])
    {
        $this->name = $name;
        $this->code = $code;
        $this->context = $context;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getContext(string $key)
    {
        return $this->context[$key];
    }
}
