<?php

namespace YaFou\Visuel\Node;

use YaFou\Visuel\CompilerInterface;

class ElseNode implements NodeInterface
{

    /**
     * @var Node
     */
    private $children;

    public function __construct(NodeInterface $children)
    {
        $this->children = $children;
    }

    public function compile(CompilerInterface $compiler): void
    {
        $compiler
            ->writePhp('else:')
            ->indent()
            ->newLine()
            ->subCompile($this->children)
            ->outdent()
            ->newLine();
    }
}
