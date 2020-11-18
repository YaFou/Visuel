<?php

namespace YaFou\Visuel;

use YaFou\Visuel\Node\NodeInterface;

interface ParserInterface
{
    public function parse(TokenStream $stream): NodeInterface;
}
