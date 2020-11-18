<?php

namespace YaFou\Visuel\Tests\Node;

use YaFou\Visuel\Node\ElseIfNode;
use YaFou\Visuel\Node\ElseNode;
use YaFou\Visuel\Node\IfNode;
use YaFou\Visuel\Node\TextNode;

class ConditionNodeTest extends NodeTestCase
{
    public function testSimpleCondition()
    {
        $this->assertSameCompiledCode(
            new IfNode('condition', new TextNode('text')),
            "<?php if (condition): ?>\n    text\n<?php endif; ?>"
        );
    }

    public function testElse()
    {
        $this->assertSameCompiledCode(
            new IfNode('condition', new TextNode('if'), new ElseNode(new TextNode('else'))),
            "<?php if (condition): ?>\n    if\n<?php else: ?>\n    else\n<?php endif; ?>"
        );
    }

    public function testElseIf()
    {
        $this->assertSameCompiledCode(
            new IfNode(
                'condition1',
                new TextNode('if'),
                new ElseIfNode(
                    'condition2',
                    new TextNode('elseif')
                )
            ),
            "<?php if (condition1): ?>\n    if\n<?php elseif(condition2): ?>\n    elseif\n<?php endif; ?>"
        );
    }

    public function testComplexCondition()
    {
        $code = <<<HTML
<?php if (condition1): ?>
    if
<?php elseif(condition2): ?>
    elseif 1
<?php elseif(condition3): ?>
    elseif 2
<?php else: ?>
    else
<?php endif; ?>
HTML;

        $this->assertSameCompiledCode(
            new IfNode(
                'condition1',
                new TextNode('if'),
                new ElseIfNode(
                    'condition2',
                    new TextNode('elseif 1'),
                    new ElseIfNode(
                        'condition3',
                        new TextNode('elseif 2'),
                        new ElseNode(
                            new TextNode('else')
                        )
                    )
                )
            ),
            $code
        );
    }
}
