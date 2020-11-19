<?php

namespace YaFou\Visuel\Node;

use YaFou\Visuel\CompilerInterface;

class CaseNode implements NodeInterface
{

    /**
     * @var string
     */
    private $value;
    /**
     * @var NodeInterface
     */
    private $children;

    public function __construct(string $value, NodeInterface $children)
    {
        $this->value = $value;
        $this->children = $children;
    }

    public function compile(CompilerInterface $compiler): void
    {
        $compiler
            ->writePhp('case ', $this->value, ':')
            ->indent()
            ->subCompile($this->children)
            ->outdent();
    }
}
