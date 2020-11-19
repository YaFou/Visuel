<?php

namespace YaFou\Visuel\Node;

use YaFou\Visuel\CompilerInterface;

class PhpNode implements NodeInterface
{

    /**
     * @var string
     */
    private $statement;

    public function __construct(string $statement)
    {
        $this->statement = $statement;
    }

    public function compile(CompilerInterface $compiler): void
    {
        $compiler->writePhp($this->statement);
    }
}
