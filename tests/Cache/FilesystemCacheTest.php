<?php

namespace YaFou\Visuel\Tests\Cache;

use FilesystemIterator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use YaFou\Visuel\Cache\FilesystemCache;
use YaFou\Visuel\Source;

class FilesystemCacheTest extends TestCase
{
    /**
     * @var string
     */
    private $directory;

    public function testHas()
    {
        $cache = $this->makeCache();
        $this->assertFalse($cache->has(new Source('name', 'code')));
    }

    private function makeCache(): FilesystemCache
    {
        return new FilesystemCache($this->directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Visuel_Tests');
    }

    public function testDirectoryIsAFile()
    {
        $this->expectException(InvalidArgumentException::class);
        touch($this->directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Visuel_Tests');
        $cache = new FilesystemCache($this->directory);
        $cache->has(new Source('name', 'code'));
    }

    public function testGetWithNoCache()
    {
        $this->expectException(InvalidArgumentException::class);
        $cache = $this->makeCache();
        $cache->get(new Source('name', 'code'));
    }

    public function testGetWithCache()
    {
        $cache = $this->makeCache();
        mkdir($this->directory);
        $hash = hash('sha256', 'name');
        file_put_contents($this->directory . DIRECTORY_SEPARATOR . $hash . '.php', 'compiled code');
        $this->assertSame('compiled code', $cache->get(new Source('name', 'code')));
    }

    public function testSet()
    {
        $cache = $this->makeCache();
        mkdir($this->directory);
        $hash = hash('sha256', 'name');
        file_put_contents($this->directory . DIRECTORY_SEPARATOR . $hash . '.php', 'compiled code 1');
        $cache->set($source = new Source('name', 'code'), 'compiled code 2');
        $this->assertSame('compiled code 2', $cache->get($source));
    }

    protected function tearDown(): void
    {
        if (null !== $this->directory) {
            if (is_file($this->directory)) {
                unlink($this->directory);

                return;
            }

            $iterator = new RecursiveDirectoryIterator($this->directory, FilesystemIterator::SKIP_DOTS);

            foreach (iterator_to_array($iterator) as $file) {
                unlink($file);
            }

            rmdir($this->directory);
        }
    }
}
