<?php

namespace YaFou\Visuel\Loader;

use YaFou\Visuel\Source;

interface LoaderInterface
{
    public function load(string $name): Source;
}
