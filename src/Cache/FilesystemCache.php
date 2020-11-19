<?php

namespace YaFou\Visuel\Cache;

use InvalidArgumentException;
use YaFou\Visuel\Source;

class FilesystemCache extends AbstractCache
{

    /**
     * @var string
     */
    private $directory;

    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    public function get(Source $source): string
    {
        if (!$this->has($source)) {
            throw new InvalidArgumentException(sprintf('The cache of "%s" does not exist', $source->getName()));
        }

        $filename = $this->getFilename($source);

        if (!is_file($filename)) {
            throw new InvalidArgumentException(sprintf('The cache of "%s" is not a file', $source->getName()));
        }

        return file_get_contents($filename);
    }

    public function has(Source $source): bool
    {
        $this->initialize();

        return file_exists($this->getFilename($source));
    }

    private function initialize(): void
    {
        if (!file_exists($this->directory)) {
            mkdir($this->directory, 0777, true);

            return;
        }

        if (!is_dir($this->directory)) {
            throw new InvalidArgumentException(sprintf('"%s" is a file', $this->directory));
        }
    }

    private function getFilename(Source $source): string
    {
        return $this->directory . DIRECTORY_SEPARATOR . $this->getKey($source) . '.php';
    }

    public function set(Source $source, string $code): void
    {
        $this->initialize();
        file_put_contents($this->getFilename($source), $code);
    }
}
