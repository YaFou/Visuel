<?php

namespace YaFou\Visuel\Node;

class PrintNode implements NodeInterface
{

    /**
     * @var string
     */
    private $statement;

    public function __construct(string $statement)
    {
        $this->statement = $statement;
    }
}
