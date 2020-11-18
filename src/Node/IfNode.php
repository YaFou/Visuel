<?php

namespace YaFou\Visuel\Node;

class IfNode implements NodeInterface
{

    /**
     * @var string
     */
    private $condition;
    /**
     * @var NodeInterface
     */
    private $children;
    /**
     * @var NodeInterface|null
     */
    private $nextNode;

    public function __construct(string $condition, NodeInterface $children, NodeInterface $nextNode = null)
    {
        $this->condition = $condition;
        $this->children = $children;
        $this->nextNode = $nextNode;
    }
}
