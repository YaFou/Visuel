<?php

namespace YaFou\Visuel\Cache;

use YaFou\Visuel\Source;

interface CacheInterface
{
    public function has(Source $source): bool;

    public function get(Source $source): string;

    public function set(Source $source, string $code): void;
}
