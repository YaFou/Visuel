<?php

namespace YaFou\Visuel\Block;

use YaFou\Visuel\Exception\ParseException;
use YaFou\Visuel\Node\ForeachNode;
use YaFou\Visuel\Node\NodeInterface;
use YaFou\Visuel\Parser;

class ForeachBlock extends AbstractBlock
{

    public function getName(): string
    {
        return 'foreach';
    }

    /**
     * @param Parser $parser
     * @param string|null $arguments
     * @return NodeInterface
     * @throws ParseException
     */
    public function parse(Parser $parser, string $arguments = null): NodeInterface
    {
        $children = $parser->waitUntilBlock('endforeach', 'else');
        $elseChildren = null;
        $stream = $parser->getStream();

        if ('else' === $stream->getToken()->getValue()) {
            $parser->parseBlock();
            $elseChildren = $parser->waitUntilBlock('endforeach');
        }

        return new ForeachNode($arguments, $children, $elseChildren);
    }

    public function expectArguments(): bool
    {
        return true;
    }
}
