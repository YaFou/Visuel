<?php

namespace YaFou\Visuel\Tests;

use PHPUnit\Framework\TestCase;
use YaFou\Visuel\Compiler;
use YaFou\Visuel\CompilerInterface;
use YaFou\Visuel\Node\NodeInterface;

class CompilerTest extends TestCase
{
    public function testWrite()
    {
        $this->assertSameCompiled(function (CompilerInterface $compiler) {
            $compiler->write('text');
        }, 'text');

        $this->assertSameCompiled(function (CompilerInterface $compiler) {
            $compiler->write('text1')->write('text2');
        }, 'text1text2');
    }

    private function assertSameCompiled(callable $compile, string $code): void
    {
        $node = $this->createMock(NodeInterface::class);
        $node->method('compile')->willReturnCallback($compile);

        $compiler = new Compiler();
        $this->assertSame($code, $compiler->compile($node));
    }

    public function testWritePhp()
    {
        $this->assertSameCompiled(function (CompilerInterface $compiler) {
            $compiler->writePhp('php')->write('text');
        }, '<?php php ?>text');
    }

    public function testNewLine()
    {
        $this->assertSameCompiled(function (CompilerInterface $compiler) {
            $compiler->write('text1')->newLine()->write('text2');
        }, "text1\ntext2");
    }

    public function testIndentation()
    {
        $code = <<<HTML
text1
    text2
    <?php php ?>
        text3
    text4
text5
HTML;

        $this->assertSameCompiled(function (CompilerInterface $compiler) {
            $compiler
                ->write('text1')
                ->indent()
                ->newLine()
                ->write('text2')
                ->newLine()
                ->writePhp('php')
                ->indent()
                ->newLine()
                ->write('text3')
                ->outdent()
                ->newLine()
                ->write('text4')
                ->outdent()
                ->newLine()
                ->write('text5');
        }, $code);
    }
}
