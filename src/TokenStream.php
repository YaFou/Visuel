<?php

namespace YaFou\Visuel;

use YaFou\Visuel\Exception\ParseException;

class TokenStream
{
    /**
     * @var Token[]
     */
    private $tokens;
    private $index = 0;

    /**
     * @param Token[] $tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }

    public function getTokens(): array
    {
        return $this->tokens;
    }

    public function nextToken(): ?Token
    {
        return $this->tokens[++$this->index] ?? null;
    }

    /**
     * @param string $type
     * @return Token
     * @throws ParseException
     */
    public function expectToken(string $type): Token
    {
        $token = $this->nextToken();

        if (null !== $token && $type === $token->getType()) {
            return $token;
        }

        throw new ParseException(sprintf('Expected a token of type %s', $type));
    }

    public function getToken(): ?Token
    {
        return $this->tokens[$this->index] ?? null;
    }
}
