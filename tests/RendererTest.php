<?php

namespace YaFou\Visuel\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use YaFou\Visuel\Cache\CacheInterface;
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

    /**
     * @throws Exception
     */
    public function testCacheHasNot()
    {
        $loader = $this->createMock(LoaderInterface::class);
        $loader->method('load')->willReturn($source = new Source('name', 'text'));

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())->method('has')->willReturn(false);
        $cache->expects($this->once())->method('set')->with($source);

        $renderer = new Renderer($loader, $cache);
        $this->assertSame('text', $renderer->render('name'));
    }

    /**
     * @throws Exception
     */
    public function testCacheHas()
    {
        $loader = $this->createMock(LoaderInterface::class);
        $loader->method('load')->willReturn($source = new Source('name', 'false text'));

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())->method('has')->willReturn(true);
        $cache->expects($this->once())->method('get')->with($source)->willReturn('text');

        $renderer = new Renderer($loader, $cache);
        $this->assertSame('text', $renderer->render('name'));
    }
}
