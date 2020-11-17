<?php

namespace YaFou\Visuel\Tests\Loader;

use FilesystemIterator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use YaFou\Visuel\Loader\FilesystemLoader;

class FilesystemLoaderTest extends TestCase
{
    /**
     * @var string[]
     */
    private $directories = [];

    public function testLoadWithOnePathAndNoTemplate()
    {
        $this->expectException(InvalidArgumentException::class);
        $loader = new FilesystemLoader($this->makeTemplateDirectory('templates'));
        $loader->load('template');
    }

    private function makeTemplateDirectory(string $directory, string ...$templates): string
    {
        mkdir(
            $this->directories[] = $directory = sys_get_temp_dir() .
                DIRECTORY_SEPARATOR .
                'Visual_Tests' .
                DIRECTORY_SEPARATOR .
                $directory,
            0777,
            true
        );

        foreach ($templates as $template) {
            file_put_contents($directory . DIRECTORY_SEPARATOR . $template, $templates);
        }

        return $directory;
    }

    public function testLoadWithOnePath()
    {
        $loader = new FilesystemLoader($this->makeTemplateDirectory('templates', 'template'));
        $this->assertSameSource($loader, 'template');
    }

    private function assertSameSource(FilesystemLoader $loader, string $name): void
    {
        $realName = $name;

        if ('@' === $name[0]) {
            $realName = substr($name, 1);
            $parts = explode('/', $realName);
            $namespace = array_shift($parts);
            $realName = implode('/', $parts);
        }

        $source = $loader->load($name);
        $this->assertSame($name, $source->getName());
        $this->assertSame($realName, $source->getCode());
        $this->assertStringEndsWith($realName, $source->getContext('path'));
    }

    public function testLoadWithTwoPaths()
    {
        $loader = new FilesystemLoader([
            $this->makeTemplateDirectory('templates1', 'template1'),
            $this->makeTemplateDirectory('templates2', 'template2')
        ]);
        $this->assertSameSource($loader, 'template1');
        $this->assertSameSource($loader, 'template2');
    }

    public function testLoadWithOneNamespace()
    {
        $loader = new FilesystemLoader($this->makeTemplateDirectory('templates', 'namespaced_template'));
        $loader->addPathsToNamespace(
            'namespace',
            $this->makeTemplateDirectory('namespaced_templates', 'namespaced_template')
        );
        $this->assertSameSource($loader, '@namespace/namespaced_template');
    }

    public function testLoadWithTwoNamespace()
    {
        $loader = new FilesystemLoader($this->makeTemplateDirectory('templates', 'namespaced_template'));
        $loader->addPathsToNamespace(
            'namespace1',
            $this->makeTemplateDirectory('namespaced_templates1', 'namespaced_template')
        );
        $loader->addPathsToNamespace(
            'namespace2',
            $this->makeTemplateDirectory('namespaced_templates2', 'namespaced_template')
        );
        $this->assertSameSource($loader, '@namespace1/namespaced_template');
    }

    public function testAddPaths()
    {
        $loader = new FilesystemLoader($this->makeTemplateDirectory('templates1'));
        $loader->addPaths($this->makeTemplateDirectory('templates2', 'template'));
        $this->assertSameSource($loader, 'template');
    }

    public function testSetPaths()
    {
        $this->expectException(InvalidArgumentException::class);
        $loader = new FilesystemLoader($this->makeTemplateDirectory('templates1', 'template'));
        $loader->setPaths($this->makeTemplateDirectory('template2'));
        $loader->load('template');
    }

    public function testSetPathsToNamespace()
    {
        $this->expectException(InvalidArgumentException::class);
        $loader = new FilesystemLoader($this->makeTemplateDirectory('templates1'));
        $loader->setPathsToNamespace('namespace', $this->makeTemplateDirectory('templates2', 'template'));
        $loader->setPathsToNamespace('namespace', $this->makeTemplateDirectory('templates3'));
        $loader->load('@namespace/template');
    }

    protected function tearDown(): void
    {
        foreach ($this->directories as $directory) {
            $iterator = new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS);

            foreach (iterator_to_array($iterator) as $file) {
                unlink($file);
            }

            rmdir($directory);
        }
    }
}
