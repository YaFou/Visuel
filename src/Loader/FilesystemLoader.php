<?php

namespace YaFou\Visuel\Loader;

use FilesystemIterator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use YaFou\Visuel\Source;

class FilesystemLoader implements LoaderInterface
{
    private const DEFAULT_NAMESPACE = '__default';
    /**
     * @var string[][]
     */
    private $namespaces;
    /**
     * @var Source[]
     */
    private $resolvedSources;

    /**
     * @param string|string[] $paths
     */
    public function __construct($paths)
    {
        $this->setPaths($paths);
    }

    /**
     * @param string|string[] $paths
     */
    public function setPaths($paths): void
    {
        $this->setPathsToNamespace(self::DEFAULT_NAMESPACE, $paths);
    }

    /**
     * @param string $namespace
     * @param string|string[] $paths
     */
    public function setPathsToNamespace(string $namespace, $paths): void
    {
        if (is_string($paths)) {
            $paths = [$paths];
        }

        $this->namespaces[$namespace] = $paths;
    }

    public function load(string $name): Source
    {
        if (isset($this->resolvedSources[$name])) {
            return $this->resolvedSources[$name];
        }

        $namespace = self::DEFAULT_NAMESPACE;
        $realName = $name;

        if ('@' === $name[0]) {
            $realName = substr($name, 1);
            $parts = explode('/', $realName);
            $namespace = array_shift($parts);
            $realName = implode('/', $parts);
        }

        foreach ($this->namespaces[$namespace] as $path) {
            $iterator = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);

            foreach (iterator_to_array($iterator) as $file) {
                $filename = substr((string)$file, strlen($path) + 1);
                $filename = str_replace('\\', '/', $filename);

                if ($realName === $filename) {
                    return $this->resolvedSources[$name] = new Source(
                        $name,
                        file_get_contents($file),
                        ['path' => (string)$file]
                    );
                }
            }
        }

        throw new InvalidArgumentException(sprintf('No template found for "%s"', $name));
    }

    /**
     * @param string|string[] $paths
     */
    public function addPaths($paths): void
    {
        $this->addPathsToNamespace(self::DEFAULT_NAMESPACE, $paths);
    }

    /**
     * @param string $namespace
     * @param string|string[] $paths
     */
    public function addPathsToNamespace(string $namespace, $paths): void
    {
        if (is_string($paths)) {
            $paths = [$paths];
        }

        $this->setPathsToNamespace($namespace, array_merge($this->namespaces[$namespace] ?? [], $paths));
    }
}
