<?php

namespace YaFou\Visuel\Node;

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
}
