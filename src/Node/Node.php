<?php

namespace YaFou\Visuel\Node;

class Node implements NodeInterface
{

    /**
     * @param NodeInterface[] $nodes
     */
    private $nodes;

    /**
     * @param NodeInterface[] $nodes
     */
    public function __construct(array $nodes)
    {
        $this->nodes = $nodes;
    }
}
