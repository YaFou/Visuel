<?php

namespace YaFou\Visuel;

use Exception;
use YaFou\Visuel\Block\AbstractBlock;
use YaFou\Visuel\Block\BreakBlock;
use YaFou\Visuel\Block\ConditionBlock;
use YaFou\Visuel\Block\ContinueBlock;
use YaFou\Visuel\Block\ForeachBlock;
use YaFou\Visuel\Block\SwitchBlock;
use YaFou\Visuel\Exception\ParseException;
use YaFou\Visuel\Node\Node;
use YaFou\Visuel\Node\NodeInterface;
use YaFou\Visuel\Node\PrintNode;
use YaFou\Visuel\Node\TextNode;

class Parser implements ParserInterface
{
    /**
     * @var AbstractBlock[]
     */
    private $blocks;
    /**
     * @var TokenStream
     */
    private $stream;

    /**
     * @param AbstractBlock[] $blocks
     */
    public function __construct(array $blocks = [])
    {
        $this->blocks = array_merge([
            new ConditionBlock(),
            new ForeachBlock(),
            new SwitchBlock(),
            new BreakBlock(),
            new ContinueBlock()
        ], $blocks);
    }

    /**
     * @param TokenStream $stream
     * @return NodeInterface
     * @throws Exception
     */
    public function parse(TokenStream $stream): NodeInterface
    {
        $nodes = [];
        $this->stream = $stream;

        while (null !== $stream->getToken()) {
            $this->doParse($nodes);
        }

        return new Node($nodes);
    }

    /**
     * @param array $nodes
     * @throws ParseException
     */
    private function doParse(array &$nodes): void
    {
        $token = $this->stream->getToken();

        switch ($token->getType()) {
            case Token::TEXT:
                $nodes[] = new TextNode($token->getValue());
                break;

            case Token::PRINT_START:
                $token = $this->stream->expectToken(Token::STATEMENT);
                $nodes[] = new PrintNode($token->getValue());
                $this->stream->expectToken(Token::PRINT_END);
                break;

            case Token::BLOCK:
                foreach ($this->blocks as $block) {
                    if ($token->getValue() === $block->getName()) {
                        $arguments = $this->parseBlock($block->expectArguments());
                        $nodes[] = $block->parse($this, $arguments);

                        break 2;
                    }
                }

                throw new ParseException(sprintf('No block found for %s', $token->getValue()));
        }

        $this->stream->nextToken();
    }

    /**
     * @param bool $expectArguments
     * @return string|null
     * @throws ParseException
     */
    public function parseBlock(bool $expectArguments = false): ?string
    {
        if ($expectArguments) {
            $this->stream->expectToken(Token::ARGUMENTS_START);
            $arguments = $this->stream->expectToken(Token::STATEMENT)->getValue();
            $this->stream->expectToken(Token::ARGUMENTS_END);

            return $arguments;
        }

        $token = $this->stream->nextToken();
        $arguments = null;

        if ($token && $token->isTypeOf(Token::ARGUMENTS_START)) {
            $arguments = $this->stream->expectToken(Token::STATEMENT)->getValue();
            $this->stream->expectToken(Token::ARGUMENTS_END);
        }

        return $arguments;
    }

    public function getStream(): TokenStream
    {
        return $this->stream;
    }

    /**
     * @param string ...$names
     * @return Node
     * @throws ParseException
     */
    public function waitUntilBlock(string ...$names): Node
    {
        $nodes = [];

        while (null !== $token = $this->stream->getToken()) {
            if (Token::BLOCK === $token->getType() && in_array($token->getValue(), $names)) {
                return new Node($nodes);
            }

            $this->doParse($nodes);
        }

        throw new ParseException(sprintf('Expected block %s', implode(', ', $names)));
    }
}
