<?php

namespace YaFou\Visuel\Block;

use YaFou\Visuel\Node\NodeInterface;
use YaFou\Visuel\Node\PhpNode;
use YaFou\Visuel\Parser;

class ContinueBlock extends AbstractBlock
{

    public function getName(): string
    {
        return 'continue';
    }

    public function parse(Parser $parser, string $arguments = null): NodeInterface
    {
        return new PhpNode('continue;');
    }
}
