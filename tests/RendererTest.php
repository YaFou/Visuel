<?php

namespace YaFou\Visuel\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use YaFou\Visuel\Loader\LoaderInterface;
use YaFou\Visuel\Renderer;
use YaFou\Visuel\Source;

class RendererTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testRender()
    {
        $loader = $this->createMock(LoaderInterface::class);
        $loader->method('load')->willReturn(new Source('name', 'text'));

        $renderer = new Renderer($loader);
        $this->assertSame('text', $renderer->render('name'));
    }

    /**
     * @throws Exception
     */
    public function testRenderWithParameters()
    {
        $loader = $this->createMock(LoaderInterface::class);
        $loader->method('load')->willReturn(new Source('name', 'text {{ $variable }}'));

        $renderer = new Renderer($loader);
        $this->assertSame('text statement', $renderer->render('name', ['variable' => 'statement']));
    }
}
