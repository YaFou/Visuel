<?php

namespace YaFou\Visuel\Node;

use YaFou\Visuel\CompilerInterface;

class ForeachNode implements NodeInterface
{

    /**
     * @var string
     */
    private $statement;
    /**
     * @var NodeInterface
     */
    private $children;
    /**
     * @var NodeInterface|null
     */
    private $elseChildren;

    public function __construct(string $statement, NodeInterface $children, NodeInterface $elseChildren = null)
    {
        $this->statement = $statement;
        $this->children = $children;
        $this->elseChildren = $elseChildren;
    }

    public function compile(CompilerInterface $compiler): void
    {
        // TODO: Implement compile() method.
    }
}
