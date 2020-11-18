<?php

namespace YaFou\Visuel\Tests;

use Exception;
use Generator;
use PHPUnit\Framework\TestCase;
use YaFou\Visuel\Block\AbstractBlock;
use YaFou\Visuel\Exception\ParseException;
use YaFou\Visuel\Node\Node;
use YaFou\Visuel\Node\PrintNode;
use YaFou\Visuel\Node\TextNode;
use YaFou\Visuel\Parser;
use YaFou\Visuel\Token;
use YaFou\Visuel\TokenStream;

class ParserTest extends TestCase
{
    /**
     * @param array $tokens
     * @param array $nodes
     * @dataProvider provideTokensToParse
     * @throws Exception
     */
    public function testParse(array $tokens, array $nodes)
    {
        $tokens = array_map(function (array $token) {
            return new Token($token[0], $token[1] ?? null);
        }, $tokens);

        $blockMock = $this->createMock(AbstractBlock::class);
        $blockMock->method('getName')->willReturn('block');
        $blockMock->method('parse')->willReturnCallback(function (Parser $parser, string $arguments = null) {
            return null === $arguments ? new Node([]) : new TextNode($arguments);
        });

        $parser = new Parser([$blockMock]);
        $this->assertEquals(new Node($nodes), $parser->parse(new TokenStream($tokens)));
    }

    public function provideTokensToParse(): Generator
    {
        yield [[[Token::TEXT, 'text']], [new TextNode('text')]];

        yield [
            // {{statement}}
            [[Token::PRINT_START], [Token::STATEMENT, 'statement'], [Token::PRINT_END]],
            [new PrintNode('statement')]
        ];

        yield [[[Token::BLOCK, 'block']], [new Node([])]];

        yield [[
            // @block(arguments)
            [Token::BLOCK, 'block'],
            [Token::ARGUMENTS_START],
            [Token::STATEMENT, 'arguments'],
            [Token::ARGUMENTS_END]
        ], [new TextNode('arguments')]];
    }

    public function testExpectedTokenException()
    {
        $this->expectException(ParseException::class);
        $parser = new Parser();
        $parser->parse(new TokenStream([new Token(Token::PRINT_START)]));
    }

    public function testExpectedArgumentsException()
    {
        $this->expectException(ParseException::class);

        $blockMock = $this->createMock(AbstractBlock::class);
        $blockMock->method('getName')->willReturn('block');
        $blockMock->method('expectArguments')->willReturn(true);

        $parser = new Parser([$blockMock]);
        $parser->parse(new TokenStream([new Token(Token::BLOCK, 'block')]));
    }

    public function testExpectedBlockNotFoundException()
    {
        $this->expectException(ParseException::class);
        $parser = new Parser();
        $parser->parse(new TokenStream([new Token(Token::BLOCK, 'block')]));
    }
}
