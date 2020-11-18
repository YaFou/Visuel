<?php

namespace YaFou\Visuel\Node;

class TextNode implements NodeInterface
{

    /**
     * @var string
     */
    private $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }
}
