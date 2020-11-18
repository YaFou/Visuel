<?php

namespace YaFou\Visuel\Node;

class ElseNode implements NodeInterface
{

    /**
     * @var Node
     */
    private $children;

    public function __construct(Node $children)
    {
        $this->children = $children;
    }
}
