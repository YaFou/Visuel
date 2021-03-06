<?php

namespace YaFou\Visuel\Node;

use YaFou\Visuel\CompilerInterface;

class ForeachNode implements NodeInterface
{

    /**
     * @var string
     */
    private $statement;
    /**
     * @var NodeInterface
     */
    private $children;
    /**
     * @var NodeInterface|null
     */
    private $elseChildren;

    public function __construct(string $statement, NodeInterface $children, NodeInterface $elseChildren = null)
    {
        $this->statement = $statement;
        $this->children = $children;
        $this->elseChildren = $elseChildren;
    }

    public function compile(CompilerInterface $compiler): void
    {
        if ($this->elseChildren) {
            preg_match('/(.+)as/', $this->statement, $matches);

            $compiler
                ->writePhp('if (!empty(', trim($matches[1]), ')):')
                ->indent();
        }

        $compiler
            ->writePhp('foreach (', $this->statement, '):')
            ->indent()
            ->subCompile($this->children)
            ->outdent()
            ->writePhp('endforeach;');

        if ($this->elseChildren) {
            $compiler
                ->outdent()
                ->writePhp('else:')
                ->indent()
                ->subCompile($this->elseChildren)
                ->outdent()
                ->writePhp('endif;');
        }
    }
}
