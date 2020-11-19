<?php

namespace YaFou\Visuel\Node;

use YaFou\Visuel\CompilerInterface;

class SwitchNode implements NodeInterface
{

    /**
     * @var string|null
     */
    private $condition;
    /**
     * @var NodeInterface
     */
    private $children;

    public function __construct(string $condition, NodeInterface $children)
    {
        $this->condition = $condition;
        $this->children = $children;
    }

    public function compile(CompilerInterface $compiler): void
    {
        $compiler
            ->writePhp('switch (', $this->condition, '):')
            ->subCompile($this->children)
            ->outdent()
            ->writePhp('endswitch;');
    }
}
