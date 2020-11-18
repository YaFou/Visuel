<?php

namespace YaFou\Visuel\Node;

use YaFou\Visuel\CompilerInterface;

class TextNode implements NodeInterface
{

    /**
     * @var string
     */
    private $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function compile(CompilerInterface $compiler): void
    {
        $compiler->write($this->text);
    }
}
