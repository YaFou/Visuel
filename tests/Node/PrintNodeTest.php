<?php

namespace YaFou\Visuel\Tests\Node;

use YaFou\Visuel\Node\PrintNode;

class PrintNodeTest extends NodeTestCase
{
    public function testCompile()
    {
        $this->assertSameCompiledCode(new PrintNode('statement'), '<?= htmlspecialchars(statement) ?>');
    }

    public function testCompileAndTrim()
    {
        $this->assertSameCompiledCode(new PrintNode(' statement '), '<?= htmlspecialchars(statement) ?>');
    }
}
