<?php

namespace YaFou\Visuel\Node;

use YaFou\Visuel\CompilerInterface;

interface NodeInterface
{
    public function compile(CompilerInterface $compiler): void;
}
