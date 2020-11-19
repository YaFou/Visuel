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
        $compiler = new Compiler();
        $this->assertSame($code, $compiler->compile($this->makeNode($compile)));
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
                ->write('text2')
                ->newLine()
                ->writePhp('php')
                ->indent()
                ->write('text3')
                ->outdent()
                ->write('text4')
                ->outdent()
                ->write('text5');
        }, $code);
    }

    public function testSubCompile()
    {
        $this->assertSameCompiled(function (CompilerInterface $compiler) {
            $compiler->subCompile($this->makeNode(function (CompilerInterface $compiler) {
                $compiler->write('text');
            }));
        }, 'text');
    }

    private function makeNode(callable $compile): NodeInterface
    {
        $node = $this->createMock(NodeInterface::class);
        $node->method('compile')->willReturnCallback($compile);

        return $node;
    }
}
