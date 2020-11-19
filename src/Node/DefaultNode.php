<?php

namespace YaFou\Visuel\Node;

use YaFou\Visuel\CompilerInterface;

class DefaultNode implements NodeInterface
{

    /**
     * @var NodeInterface
     */
    private $children;

    public function __construct(NodeInterface $children)
    {
        $this->children = $children;
    }

    public function compile(CompilerInterface $compiler): void
    {
        $compiler
            ->writePhp('default:')
            ->indent()
            ->subCompile($this->children)
            ->outdent();
    }
}
