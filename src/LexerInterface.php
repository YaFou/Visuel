<?php

namespace YaFou\Visuel;

interface LexerInterface
{
    public function tokenize(Source $source): TokenStream;
}
