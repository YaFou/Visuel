<?php

namespace YaFou\Visuel\Block;

use YaFou\Visuel\Node\NodeInterface;
use YaFou\Visuel\Node\PhpNode;
use YaFou\Visuel\Parser;

class BreakBlock extends AbstractBlock
{

    public function getName(): string
    {
        return 'break';
    }

    public function parse(Parser $parser, string $arguments = null): NodeInterface
    {
        return new PhpNode('break;');
    }
}
