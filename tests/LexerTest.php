<?php

namespace YaFou\Visuel\Tests;

use Exception;
use Generator;
use PHPUnit\Framework\TestCase;
use YaFou\Visuel\Exception\ParseException;
use YaFou\Visuel\Lexer;
use YaFou\Visuel\Source;
use YaFou\Visuel\Token;

class LexerTest extends TestCase
{
    /**
     * @param string $template
     * @param array $tokens
     * @dataProvider provideTemplatesToTokenize
     * @throws Exception
     */
    public function testTokenize(string $template, array $tokens)
    {
        $tokens = array_map(function (array $token) {
            return new Token($token[0], $token[1] ?? null);
        }, $tokens);

        $lexer = new Lexer();
        $this->assertEquals($tokens, $lexer->tokenize(new Source('name', $template))->getTokens());
    }

    public function provideTemplatesToTokenize(): Generator
    {
        yield ['text', [[Token::TEXT, 'text']]];
        yield ['(', [[Token::ARGUMENTS_START]]];
        yield ['\(', [[Token::TEXT, '(']]];
        yield ['text(', [[Token::TEXT, 'text'], [Token::ARGUMENTS_START]]];
        yield ['text($statement', [[Token::TEXT, 'text'], [Token::ARGUMENTS_START], [Token::STATEMENT, '$statement']]];

        yield ['text($statement)', [
            [Token::TEXT, 'text'],
            [Token::ARGUMENTS_START],
            [Token::STATEMENT, '$statement'],
            [Token::ARGUMENTS_END]
        ]];

        yield ['\\\(', [[Token::TEXT, '\\\\'], [Token::ARGUMENTS_START]]];
        yield ['\\\text', [[Token::TEXT, '\\\text']]];
        yield ['{{', [[Token::PRINT_START]]];
        yield ['\{{', [[Token::TEXT, '{{']]];
        yield ['{{$statement}}', [[Token::PRINT_START], [Token::STATEMENT, '$statement'], [Token::PRINT_END]]];
        yield ['@block', [[Token::BLOCK, 'block']]];

        yield ['@{{$statement}}', [
            [Token::TEXT, '@'],
            [Token::PRINT_START],
            [Token::STATEMENT, '$statement'],
            [Token::PRINT_END]
        ]];

        yield ['@block($arguments)', [
            [Token::BLOCK, 'block'],
            [Token::ARGUMENTS_START],
            [Token::STATEMENT, '$arguments'],
            [Token::ARGUMENTS_END]
        ]];
    }

    /**
     * @throws Exception
     */
    public function testSyntaxErrorOnStatement()
    {
        $this->expectException(ParseException::class);
        $lexer = new Lexer();
        $lexer->tokenize(new Source('name', '{{(}}'));
    }
}
