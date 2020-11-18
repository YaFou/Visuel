<?php

namespace YaFou\Visuel\Node;

use YaFou\Visuel\CompilerInterface;

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

    public function compile(CompilerInterface $compiler): void
    {
        $compiler
            ->writePhp('if (', $this->condition, '):')
            ->indent()
            ->newLine()
            ->subCompile($this->children)
            ->outdent()
            ->newLine();

        if (null !== $this->nextNode) {
            $compiler->subCompile($this->nextNode);
        }

        $compiler->writePhp('endif;');
    }
}
