<?php

namespace YaFou\Visuel\Node;

use YaFou\Visuel\CompilerInterface;

class PrintNode implements NodeInterface
{

    /**
     * @var string
     */
    private $statement;

    public function __construct(string $statement)
    {
        $this->statement = $statement;
    }

    public function compile(CompilerInterface $compiler): void
    {
        $compiler->write('<?= htmlspecialchars(', trim($this->statement), ') ?>');
    }
}
