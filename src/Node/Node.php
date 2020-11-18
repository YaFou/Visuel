<?php

namespace YaFou\Visuel\Node;

use YaFou\Visuel\CompilerInterface;

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

    public function compile(CompilerInterface $compiler): void
    {
        // TODO: Implement compile() method.
    }
}
