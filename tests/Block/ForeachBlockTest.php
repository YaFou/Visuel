<?php

namespace YaFou\Visuel\Tests\Block;

use Exception;
use PHPUnit\Framework\TestCase;
use YaFou\Visuel\Node\ForeachNode;
use YaFou\Visuel\Node\Node;
use YaFou\Visuel\Node\NodeInterface;
use YaFou\Visuel\Node\TextNode;
use YaFou\Visuel\Parser;
use YaFou\Visuel\Token;
use YaFou\Visuel\TokenStream;

class ForeachBlockTest extends TestCase
{
    private const START_TOKENS = [
        [Token::BLOCK, 'foreach'],
        [Token::ARGUMENTS_START],
        [Token::STATEMENT, 'statement'],
        [Token::ARGUMENTS_END]
    ];

    /**
     * @throws Exception
     */
    public function testExpectArguments()
    {
        $this->expectExceptionMessageMatches('/arguments_start/');
        $this->parseTokens([Token::BLOCK, 'foreach']);
    }

    /**
     * @param array ...$tokens
     * @return NodeInterface
     * @throws Exception
     */
    private function parseTokens(array ...$tokens): NodeInterface
    {
        $tokens = array_map(function (array $token) {
            return new Token($token[0], $token[1] ?? null);
        }, $tokens);

        $parser = new Parser();

        return $parser->parse(new TokenStream($tokens));
    }

    /**
     * @throws Exception
     */
    public function testExpectEndForeach()
    {
        $this->expectExceptionMessageMatches('/endforeach, else/');
        $this->parseTokens(...self::START_TOKENS);
    }

    /**
     * @throws Exception
     */
    public function testForeachAndEndForeach()
    {
        $node = $this->parseTokens(...array_merge(
        // @foreach(statement) text @endforeach
            self::START_TOKENS,
            [[Token::TEXT, 'text'], [Token::BLOCK, 'endforeach']]
        ));

        $this->assertEquals(new Node([
            new ForeachNode(
                'statement',
                new Node([
                    new TextNode('text')
                ])
            )
        ]), $node);
    }

    /**
     * @throws Exception
     */
    public function testExpectedEndForeachWithElse()
    {
        $this->expectExceptionMessageMatches('/endforeach/');
        $this->parseTokens(...array_merge(
        // @foreach(statement) text @endforeach
            self::START_TOKENS,
            [[Token::TEXT, 'text'], [Token::BLOCK, 'else']]
        ));
    }

    /**
     * @throws Exception
     */
    public function testForeachElseAndEndForeach()
    {
        $node = $this->parseTokens(...array_merge(
        // @foreach(statement) text @else empty @endforeach
            self::START_TOKENS,
            [[Token::TEXT, 'text'], [Token::BLOCK, 'else'], [Token::TEXT, 'empty'], [Token::BLOCK, 'endforeach']]
        ));

        $this->assertEquals(new Node([
            new ForeachNode(
                'statement',
                new Node([
                    new TextNode('text')
                ]),
                new Node([
                    new TextNode('empty')
                ])
            )
        ]), $node);
    }
}
