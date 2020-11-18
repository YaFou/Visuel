<?php

namespace YaFou\Visuel;

use YaFou\Visuel\Node\NodeInterface;

interface CompilerInterface
{
    public function compile(NodeInterface $node): string;

    public function write(string ...$code): self;

    public function writePhp(string ...$code): self;

    public function indent(): self;

    public function subCompile(NodeInterface $node): self;

    public function outdent(): self;

    public function newLine(): self;
}
