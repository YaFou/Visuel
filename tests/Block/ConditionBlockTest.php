<?php

namespace YaFou\Visuel\Tests\Block;

use Exception;
use PHPUnit\Framework\TestCase;
use YaFou\Visuel\Node\ElseIfNode;
use YaFou\Visuel\Node\ElseNode;
use YaFou\Visuel\Node\IfNode;
use YaFou\Visuel\Node\Node;
use YaFou\Visuel\Node\NodeInterface;
use YaFou\Visuel\Node\TextNode;
use YaFou\Visuel\Parser;
use YaFou\Visuel\Token;
use YaFou\Visuel\TokenStream;

class ConditionBlockTest extends TestCase
{
    private const START_TOKENS = [
        [Token::BLOCK, 'if'],
        [Token::ARGUMENTS_START],
        [Token::STATEMENT, 'condition'],
        [Token::ARGUMENTS_END]
    ];

    /**
     * @throws Exception
     */
    public function testExpectArguments()
    {
        $this->expectExceptionMessageMatches('/arguments_start/');
        $this->parseTokens([Token::BLOCK, 'if']);
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
    public function testExpectEndIf()
    {
        $this->expectExceptionMessageMatches('/endif, elseif, else/');
        $this->parseTokens(...self::START_TOKENS);
    }

    /**
     * @throws Exception
     */
    public function testIfAndEndIf()
    {
        $node = $this->parseTokens(...array_merge(
        // @if(condition) text @endif
            self::START_TOKENS,
            [[Token::TEXT, 'text'], [Token::BLOCK, 'endif']]
        ));

        $this->assertEquals(new Node([
            new IfNode(
                'condition',
                new Node([
                    new TextNode('text')
                ])
            )
        ]), $node);
    }

    /**
     * @throws Exception
     */
    public function testExpectedEndIfWithElse()
    {
        $this->expectExceptionMessageMatches('/endif/');
        $this->parseTokens(...array_merge(
        // @if(condition) text @else
            self::START_TOKENS,
            [[Token::TEXT, 'text'], [Token::BLOCK, 'else']]
        ));
    }

    /**
     * @throws Exception
     */
    public function testIfElseAndEndIf()
    {
        $node = $this->parseTokens(...array_merge(
        // @if(condition) true @else false @endif
            self::START_TOKENS,
            [[Token::TEXT, 'true'], [Token::BLOCK, 'else'], [Token::TEXT, 'false'], [Token::BLOCK, 'endif']]
        ));

        $this->assertEquals(new Node([
            new IfNode(
                'condition',
                new Node([
                    new TextNode('true')
                ]),
                new ElseNode(
                    new Node([
                        new TextNode('false')
                    ])
                )
            )
        ]), $node);
    }

    /**
     * @throws Exception
     */
    public function testExpectArgumentsToElseIf()
    {
        $this->expectExceptionMessageMatches('/arguments_start/');
        $this->parseTokens(...array_merge(
        // @if(condition) text @endif
            self::START_TOKENS,
            [[Token::TEXT, 'text'], [Token::BLOCK, 'elseif']]
        ));
    }

    /**
     * @throws Exception
     */
    public function testIfElseIfAndEndIf()
    {
        $node = $this->parseTokens(...array_merge(
        // @if(condition) true @elseif(condition2) false @endif
            self::START_TOKENS,
            [
                [Token::TEXT, 'true'],
                [Token::BLOCK, 'elseif'],
                [Token::ARGUMENTS_START],
                [Token::STATEMENT, 'condition2'],
                [Token::ARGUMENTS_END],
                [Token::TEXT, 'false'],
                [Token::BLOCK, 'endif']
            ]
        ));

        $this->assertEquals(new Node([
            new IfNode(
                'condition',
                new Node([
                    new TextNode('true')
                ]),
                new ElseIfNode(
                    'condition2',
                    new Node([
                        new TextNode('false')
                    ])
                )
            )
        ]), $node);
    }

    /**
     * @throws Exception
     */
    public function testComplexConditions()
    {
        $node = $this->parseTokens(...array_merge(
        // @if(condition)
        //     if
        // @elseif(condition2)
        //     elseif 1
        // @elseif(condition3)
        //     elseif 2
        // @else
        //     else
        // @endif
            self::START_TOKENS,
            [
                [Token::TEXT, 'if'],
                [Token::BLOCK, 'elseif'],
                [Token::ARGUMENTS_START],
                [Token::STATEMENT, 'condition2'],
                [Token::ARGUMENTS_END],
                [Token::TEXT, 'elseif 1'],
                [Token::BLOCK, 'elseif'],
                [Token::ARGUMENTS_START],
                [Token::STATEMENT, 'condition3'],
                [Token::ARGUMENTS_END],
                [Token::TEXT, 'elseif 2'],
                [Token::BLOCK, 'else'],
                [Token::TEXT, 'else'],
                [Token::BLOCK, 'endif']
            ]
        ));

        $this->assertEquals(new Node([
            new IfNode(
                'condition',
                new Node([
                    new TextNode('if')
                ]),
                new ElseIfNode(
                    'condition2',
                    new Node([
                        new TextNode('elseif 1')
                    ]),
                    new ElseIfNode(
                        'condition3',
                        new Node([
                            new TextNode('elseif 2')
                        ]),
                        new ElseNode(
                            new Node([
                                new TextNode('else')
                            ])
                        )
                    )
                )
            )
        ]), $node);
    }
}
