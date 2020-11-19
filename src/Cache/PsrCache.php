<?php

namespace YaFou\Visuel\Cache;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use YaFou\Visuel\Source;

class PsrCache extends AbstractCache
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    public function __construct(CacheItemPoolInterface $cache)
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
        return $this->cache->hasItem($this->getKey($source));
    }

    /**
     * @param Source $source
     * @return string
     * @throws InvalidArgumentException
     */
    public function get(Source $source): string
    {
        return $this->cache->getItem($this->getKey($source))->get();
    }

    /**
     * @param Source $source
     * @param string $code
     * @throws InvalidArgumentException
     */
    public function set(Source $source, string $code): void
    {
        $item = $this->cache->getItem($this->getKey($source));

        if (!$item->isHit()) {
            $item->set($code);
            $this->cache->save($item);
        }
    }
}
