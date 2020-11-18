<?php

namespace YaFou\Visuel\Tests\Node;

use YaFou\Visuel\Node\Node;
use YaFou\Visuel\Node\TextNode;

class NodeTest extends NodeTestCase
{
    public function testCompile()
    {
        $this->assertSameCompiledCode(
            new Node([new TextNode('text1'), new TextNode('text2')]),
            'text1text2'
        );
    }
}
