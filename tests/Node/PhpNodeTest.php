<?php

namespace YaFou\Visuel\Tests\Node;

use YaFou\Visuel\Node\PhpNode;

class PhpNodeTest extends NodeTestCase
{
    public function testCompile()
    {
        $this->assertSameCompiledCode(new PhpNode('statement'), '<?php statement ?>');
    }
}
