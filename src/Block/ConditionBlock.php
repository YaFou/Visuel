<?php

namespace YaFou\Visuel\Block;

use YaFou\Visuel\Exception\ParseException;
use YaFou\Visuel\Node\ElseIfNode;
use YaFou\Visuel\Node\ElseNode;
use YaFou\Visuel\Node\IfNode;
use YaFou\Visuel\Node\NodeInterface;
use YaFou\Visuel\Parser;
use YaFou\Visuel\TokenStream;

class ConditionBlock extends AbstractBlock
{

    /**
     * @var Parser
     */
    private $parser;
    /**
     * @var TokenStream
     */
    private $stream;

    public function getName(): string
    {
        return 'if';
    }

    /**
     * @param Parser $parser
     * @param string|null $arguments
     * @return NodeInterface
     * @throws ParseException
     */
    public function parse(Parser $parser, string $arguments = null): NodeInterface
    {
        $this->parser = $parser;
        $this->stream = $parser->getStream();

        $children = $parser->waitUntilBlock('endif', 'elseif', 'else');
        $nextNode = $this->parseNextNode();

        return new IfNode($arguments, $children, $nextNode);
    }

    /**
     * @return NodeInterface
     * @throws ParseException
     */
    private function parseNextNode(): ?NodeInterface
    {
        $token = $this->stream->getToken();
        $nextNode = null;

        switch ($token->getValue()) {
            case 'else':
                $this->parser->parseBlock();
                $nextNode = new ElseNode($this->parser->waitUntilBlock('endif'));
                $this->stream->nextToken();
                break;

            case 'elseif':
                $condition = $this->parser->parseBlock(true);

                $nextNode = new ElseIfNode(
                    $condition,
                    $this->parser->waitUntilBlock('endif', 'elseif', 'else'),
                    $this->parseNextNode()
                );

                $this->stream->nextToken();
                break;
        }

        return $nextNode;
    }

    public function expectArguments(): bool
    {
        return true;
    }
}
