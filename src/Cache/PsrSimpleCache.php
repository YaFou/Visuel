<?php

namespace YaFou\Visuel\Cache;

use Psr\SimpleCache\CacheInterface as PsrCacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use YaFou\Visuel\Source;

class PsrSimpleCache extends AbstractCache
{
    /**
     * @var PsrCacheInterface
     */
    private $cache;

    public function __construct(PsrCacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param Source $source
     * @return bool
     * @throws InvalidArgumentException
     */
    public function has(Source $source): bool
    {
        return $this->cache->has($this->getKey($source));
    }

    /**
     * @param Source $source
     * @return string
     * @throws InvalidArgumentException
     */
    public function get(Source $source): string
    {
        return $this->cache->get($this->getKey($source));
    }

    /**
     * @param Source $source
     * @param string $code
     * @throws InvalidArgumentException
     */
    public function set(Source $source, string $code): void
    {
        $this->cache->set($this->getKey($source), $code);
    }
}
