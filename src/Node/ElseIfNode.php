<?php

namespace YaFou\Visuel\Node;

use YaFou\Visuel\CompilerInterface;

class ElseIfNode implements NodeInterface
{

    /**
     * @var string|null
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

    public function __construct(?string $condition, NodeInterface $children, NodeInterface $nextNode = null)
    {
        $this->condition = $condition;
        $this->children = $children;
        $this->nextNode = $nextNode;
    }

    public function compile(CompilerInterface $compiler): void
    {
        $compiler
            ->writePhp('elseif(', $this->condition, '):')
            ->indent()
            ->newLine()
            ->subCompile($this->children)
            ->outdent()
            ->newLine();

        if ($this->nextNode) {
            $compiler->subCompile($this->nextNode);
        }
    }
}
