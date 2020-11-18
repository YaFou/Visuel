<?php

namespace YaFou\Visuel\Tests\Node;

use YaFou\Visuel\Node\ForeachNode;
use YaFou\Visuel\Node\TextNode;

class ForeachNodeTest extends NodeTestCase
{
    public function testSimpleLoop()
    {
        $this->assertSameCompiledCode(
            new ForeachNode('statement', new TextNode('text')),
            "<?php foreach (statement): ?>\n    text\n<?php endforeach; ?>"
        );
    }

    public function testLoopAndElse()
    {
        $code = <<<'HTML'
<?php if (!empty($items)): ?>
    <?php foreach ($items as $item): ?>
        loop
    <?php endforeach; ?>
<?php else: ?>
    else
<?php endif; ?>
HTML;

        $this->assertSameCompiledCode(
            new ForeachNode('$items as $item', new TextNode('loop'), new TextNode('else')),
            $code
        );
    }
}
