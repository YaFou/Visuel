<?php

namespace YaFou\Visuel\Tests\Node;

use YaFou\Visuel\Node\TextNode;

class TextNodeTest extends NodeTestCase
{
    public function testCompile()
    {
        $this->assertSameCompiledCode(new TextNode('text'), 'text');
    }
}
