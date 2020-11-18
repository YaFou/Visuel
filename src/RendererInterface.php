<?php

namespace YaFou\Visuel;

interface RendererInterface
{
    public function render(string $name, array $parameters = []): string;
}
