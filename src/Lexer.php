<?php

namespace YaFou\Visuel;

use Exception;
use ParseError;
use YaFou\Visuel\Exception\ParseException;

class Lexer implements LexerInterface
{
    public const BLOCK_NAME_PATTERN = 'block_name_pattern';
    public const STATE_TEXT = 'state_text';
    public const STATE_ARGUMENTS = 'state_arguments';
    public const STATE_PRINT = 'state_print';
    private const TEXT_TOKENS = [Token::ARGUMENTS_START, Token::PRINT_START, Token::BLOCK];

    /**
     * @var array
     */
    private $options;
    /**
     * @var string
     */
    private $code;
    /**
     * @var int
     */
    private $index;
    /**
     * @var int
     */
    private $line;
    /**
     * @var int
     */
    private $column;
    /**
     * @var Token[]
     */
    private $tokens;
    /**
     * @var string
     */
    private $buffer;
    /**
     * @var string
     */
    private $state;
    /**
     * @var string|null
     */
    private $char;
    /**
     * @var Source
     */
    private $source;

    public function __construct(array $options = [])
    {
        $this->options = array_merge([
            Token::PRINT_START => '{{',
            Token::PRINT_END => '}}',
            Token::BLOCK => '@',
            self::BLOCK_NAME_PATTERN => '/^[a-zA-Z_][\w_]*$/',
            Token::ARGUMENTS_START => '(',
            Token::ARGUMENTS_END => ')'
        ], $options);
    }

    /**
     * @param Source $source
     * @return TokenStream
     * @throws Exception
     */
    public function tokenize(Source $source): TokenStream
    {
        $this->source = $source;
        $this->code = $source->getCode();
        $this->index = 0;
        $this->line = 1;
        $this->column = 0;
        $this->tokens = [];
        $this->buffer = '';
        $this->char = '';
        $this->state = self::STATE_TEXT;

        while (null !== $this->char) {
            $this->nextChar();

            switch ($this->state) {
                case self::STATE_TEXT:
                    $this->tokenizeText();
                    break;

                case self::STATE_ARGUMENTS:
                    $this->tokenizeArguments();
                    break;

                case self::STATE_PRINT:
                    $this->tokenizePrint();
                    break;

                default:
                    throw new Exception('Unexpected state');
            }
        }

        return new TokenStream($this->tokens);
    }

    private function nextChar(int $times = 1): void
    {
        for ($i = 0; $i < $times; $i++) {
            $this->char = $this->code[$this->index++] ?? null;
            $this->column++;

            if ("\n" === $this->char) {
                $this->line++;
                $this->column = 0;
            }
        }
    }

    /**
     * @throws ParseException
     */
    private function tokenizeText(): void
    {
        if (null === $this->char) {
            $this->makeTokenFromBuffer(Token::TEXT);

            return;
        }

        if ('\\' === $this->char) {
            if ('\\' === $this->getChar(1)) {
                foreach (self::TEXT_TOKENS as $test) {
                    $test = $this->options[$test];

                    if (false !== $times = $this->testString($test, 2)) {
                        $this->appendBuffer('\\\\');
                        $this->nextChar();

                        return;
                    }
                }

                $this->appendBuffer('\\');
                $this->nextChar();
            }

            foreach (self::TEXT_TOKENS as $test) {
                $test = $this->options[$test];

                if (false !== $times = $this->testString($test, 1)) {
                    $text = $this->getChars(strlen($test), 1);
                    $this->nextChar($times);
                    $this->appendBuffer($text);

                    return;
                }
            }
        }

        foreach (self::TEXT_TOKENS as $token) {
            if (Token::BLOCK === $token) {
                if (false !== $times = $this->testString($this->options[Token::BLOCK])) {
                    $length = 1;
                    $oldBlockName = null;

                    do {
                        $blockName = $this->getChars($length, 1);
                        $length++;

                        if ($oldBlockName === $blockName) {
                            $blockName .= ' ';

                            break;
                        }

                        $oldBlockName = $blockName;
                    } while (preg_match($this->options[self::BLOCK_NAME_PATTERN], $blockName));

                    $blockName = substr($blockName, 0, -1);

                    if ('' === $blockName) {
                        $this->appendBuffer($this->options[Token::BLOCK]);

                        return;
                    }

                    $this->makeTokenFromBuffer(Token::TEXT);
                    $this->nextChar($length - 2);
                    $this->makeToken(Token::BLOCK, $blockName);

                    return;
                }

                continue;
            }

            if (
            $this->makeTokenIfStringTested(
                $this->options[$token],
                $token,
                Token::TEXT,
                Token::ARGUMENTS_START === $token ? self::STATE_ARGUMENTS : self::STATE_PRINT
            )
            ) {
                return;
            }
        }

        $this->appendBuffer($this->char);
    }

    private function makeTokenFromBuffer(string $type): void
    {
        if ('' === $this->buffer) {
            return;
        }

        $this->makeToken($type, $this->buffer);
        $this->buffer = '';
    }

    private function makeToken(string $type, string $value = null): void
    {
        $this->tokens[] = new Token($type, $value);
    }

    private function getChar(int $offset): ?string
    {
        return $this->code[$this->index + $offset - 1] ?? null;
    }

    /**
     * @param string $test
     * @param int $offset
     * @return false|int
     */
    private function testString(string $test, int $offset = 0)
    {
        return $test === $this->getChars(strlen($test), $offset) ? strlen($test) - 1 + $offset : false;
    }

    /**
     * @param int $length
     * @param int $offset
     * @return string
     */
    private function getChars(int $length, int $offset = 0): string
    {
        $text = '';

        for ($i = 0; $i < $length; $i++) {
            $text .= $this->getChar($offset + $i);
        }

        return $text;
    }

    private function appendBuffer(string $buffer): void
    {
        $this->buffer .= $buffer;
    }

    /**
     * @param string $test
     * @param string $token
     * @param string $bufferToken
     * @param string $state
     * @return bool
     * @throws ParseException
     */
    private function makeTokenIfStringTested(string $test, string $token, string $bufferToken, string $state): bool
    {
        if (false !== $times = $this->testString($test)) {
            $this->nextChar($times);

            if (Token::STATEMENT === $bufferToken) {
                $this->checkPhpSyntax();
            }

            $this->makeTokenFromBuffer($bufferToken);
            $this->makeToken($token);
            $this->state = $state;

            return true;
        }

        return false;
    }

    /**
     * @throws ParseException
     */
    private function checkPhpSyntax(): void
    {
        try {
            eval('return;' . $this->buffer . ';');
        } catch (ParseError $e) {
            $this->throwParseException($e->getMessage());
        }
    }

    /**
     * @param string $message
     * @throws ParseException
     */
    private function throwParseException(string $message): void
    {
        throw new ParseException(sprintf(
            'Syntax exception at "%s" line %d column %d: %s',
            $this->source->getName(),
            $this->line,
            $this->column,
            $message
        ));
    }

    /**
     * @throws ParseException
     */
    private function tokenizeArguments(): void
    {
        if ($this->makeStatementTokenIfEnd()) {
            return;
        }

        if (
        $this->makeTokenIfStringTested(
            $this->options[Token::ARGUMENTS_END],
            Token::ARGUMENTS_END,
            Token::STATEMENT,
            self::STATE_TEXT
        )
        ) {
            return;
        }

        $this->appendBuffer($this->char);
    }

    /**
     * @throws ParseException
     */
    private function makeStatementTokenIfEnd(): bool
    {
        if (null === $this->char) {
            $this->checkPhpSyntax();
            $this->makeTokenFromBuffer(Token::STATEMENT);

            return true;
        }

        return false;
    }

    /**
     * @throws ParseException
     */
    private function tokenizePrint(): void
    {
        if ($this->makeStatementTokenIfEnd()) {
            return;
        }

        if (
        $this->makeTokenIfStringTested(
            $this->options[Token::PRINT_END],
            Token::PRINT_END,
            Token::STATEMENT,
            self::STATE_TEXT
        )
        ) {
            return;
        }

        $this->appendBuffer($this->char);
    }
}
