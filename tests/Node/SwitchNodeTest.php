<?php

namespace YaFou\Visuel\Tests\Node;

use YaFou\Visuel\Node\CaseNode;
use YaFou\Visuel\Node\DefaultNode;
use YaFou\Visuel\Node\Node;
use YaFou\Visuel\Node\SwitchNode;
use YaFou\Visuel\Node\TextNode;

class SwitchNodeTest extends NodeTestCase
{
    public function testEmptySwitch()
    {
        $this->assertSameCompiledCode(
            new SwitchNode('condition', new Node([])),
            "<?php switch (condition): ?>\n<?php endswitch; ?>"
        );
    }

    public function testComplexSwitch()
    {
        $code = <<<HTML
<?php switch (condition): ?><?php case value 1: ?>
    text 1
<?php default: ?>
    default
<?php case value 2: ?>
    text 2

<?php endswitch; ?>
HTML;

        $this->assertSameCompiledCode(
            new SwitchNode('condition', new Node([
                new CaseNode('value 1', new TextNode('text 1')),
                new DefaultNode(new TextNode('default')),
                new CaseNode('value 2', new TextNode('text 2'))
            ])),
            $code
        );
    }
}
