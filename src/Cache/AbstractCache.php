<?php

namespace YaFou\Visuel\Cache;

use YaFou\Visuel\Source;

abstract class AbstractCache implements CacheInterface
{
    protected function getKey(Source $source): string
    {
        return hash('sha256', $source->getName());
    }
}
