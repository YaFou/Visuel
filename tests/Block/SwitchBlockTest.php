<?php

namespace YaFou\Visuel\Tests\Block;

use Exception;
use PHPUnit\Framework\TestCase;
use YaFou\Visuel\Node\CaseNode;
use YaFou\Visuel\Node\DefaultNode;
use YaFou\Visuel\Node\Node;
use YaFou\Visuel\Node\NodeInterface;
use YaFou\Visuel\Node\SwitchNode;
use YaFou\Visuel\Node\TextNode;
use YaFou\Visuel\Parser;
use YaFou\Visuel\Token;
use YaFou\Visuel\TokenStream;

class SwitchBlockTest extends TestCase
{
    private const START_TOKENS = [
        [Token::BLOCK, 'switch'],
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
        $this->parseTokens([Token::BLOCK, 'switch']);
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
    public function testExpectBlock()
    {
        $this->expectExceptionMessageMatches('/case, endswitch, default/');
        $this->parseTokens(...self::START_TOKENS);
    }

    /**
     * @throws Exception
     */
    public function testEmptySwitch()
    {
        $node = $this->parseTokens(...array_merge(
        // @switch(statement) text @endswitch
            self::START_TOKENS,
            [[Token::TEXT, 'text'], [Token::BLOCK, 'endswitch']]
        ));

        $this->assertEquals(new Node([
            new SwitchNode('statement', new Node([]))
        ]), $node);
    }

    /**
     * @throws Exception
     */
    public function testExpectArgumentsToCase()
    {
        $this->expectExceptionMessageMatches('/arguments_start/');
        $this->parseTokens(...array_merge(
        // @switch(statement) @case
            self::START_TOKENS,
            [[Token::BLOCK, 'case']]
        ));
    }

    /**
     * @throws Exception
     */
    public function testOneCase()
    {
        $node = $this->parseTokens(...array_merge(
        // @switch(statement) @case(value) text @endswitch
            self::START_TOKENS,
            [
                [Token::BLOCK, 'case'],
                [Token::ARGUMENTS_START],
                [Token::STATEMENT, 'value'],
                [Token::ARGUMENTS_END],
                [Token::TEXT, 'text'],
                [Token::BLOCK, 'endswitch']
            ]
        ));

        $this->assertEquals(new Node([
            new SwitchNode('statement', new Node([
                new CaseNode('value', new Node([
                    new TextNode('text')
                ]))
            ]))
        ]), $node);
    }

    /**
     * @throws Exception
     */
    public function testTwoCases()
    {
        $node = $this->parseTokens(...array_merge(
        // @switch(statement) @case(value1) text1 @case(value2) text2 @endswitch
            self::START_TOKENS,
            [
                [Token::BLOCK, 'case'],
                [Token::ARGUMENTS_START],
                [Token::STATEMENT, 'value1'],
                [Token::ARGUMENTS_END],
                [Token::TEXT, 'text1'],
                [Token::BLOCK, 'case'],
                [Token::ARGUMENTS_START],
                [Token::STATEMENT, 'value2'],
                [Token::ARGUMENTS_END],
                [Token::TEXT, 'text2'],
                [Token::BLOCK, 'endswitch']
            ]
        ));

        $this->assertEquals(new Node([
            new SwitchNode('statement', new Node([
                new CaseNode('value1', new Node([
                    new TextNode('text1')
                ])),
                new CaseNode('value2', new Node([
                    new TextNode('text2')
                ]))
            ]))
        ]), $node);
    }

    /**
     * @throws Exception
     */
    public function testDefault()
    {
        $node = $this->parseTokens(...array_merge(
        // @switch(statement) @default text @endswitch
            self::START_TOKENS,
            [[Token::BLOCK, 'default'], [Token::TEXT, 'text'], [Token::BLOCK, 'endswitch']]
        ));

        $this->assertEquals(new Node([
            new SwitchNode('statement', new Node([
                new DefaultNode(new Node([
                    new TextNode('text')
                ]))
            ]))
        ]), $node);
    }

    /**
     * @throws Exception
     */
    public function testExpectNotDefaultTwice()
    {
        $this->expectExceptionMessageMatches('/No block found for default/');
        $this->parseTokens(...array_merge(
        // @switch(statement) @default text @default @endswitch
            self::START_TOKENS,
            [[Token::BLOCK, 'default'], [Token::TEXT, 'text'], [Token::BLOCK, 'default'], [Token::BLOCK, 'endswitch']]
        ));
    }

    /**
     * @throws Exception
     */
    public function testExpectNotDefaultTwiceWithOneCase()
    {
        $this->expectExceptionMessageMatches('/No block found for default/');
        $this->parseTokens(...array_merge(
        // @switch(statement) @default default text @case(value) case text @default @endswitch
            self::START_TOKENS,
            [
                [Token::BLOCK, 'default'],
                [Token::TEXT, 'default text'],
                [Token::BLOCK, 'case'],
                [Token::ARGUMENTS_START],
                [Token::STATEMENT, 'value'],
                [Token::ARGUMENTS_END],
                [Token::TEXT, 'case text'],
                [Token::BLOCK, 'default'],
                [Token::BLOCK, 'endswitch']
            ]
        ));
    }
}
