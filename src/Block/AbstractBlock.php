<?php

namespace YaFou\Visuel\Block;

use YaFou\Visuel\Node\NodeInterface;
use YaFou\Visuel\Parser;

abstract class AbstractBlock
{
    abstract public function getName(): string;

    abstract public function parse(Parser $parser, string $arguments = null): NodeInterface;

    public function expectArguments(): bool
    {
        return false;
    }
}
