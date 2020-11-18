<?php

namespace YaFou\Visuel\Tests\Node;

use PHPUnit\Framework\TestCase;
use YaFou\Visuel\Compiler;
use YaFou\Visuel\Node\NodeInterface;

class NodeTestCase extends TestCase
{
    protected static function assertSameCompiledCode(NodeInterface $node, string $code): void
    {
        $compiler = new Compiler();
        self::assertSame($code, $compiler->compile($node));
    }
}
