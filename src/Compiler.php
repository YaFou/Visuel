<?php

namespace YaFou\Visuel;

use YaFou\Visuel\Node\NodeInterface;

class Compiler implements CompilerInterface
{

    /**
     * @var string
     */
    private $code;
    /**
     * @var int
     */
    private $indentation = 0;

    public function compile(NodeInterface $node): string
    {
        $this->code = '';
        $this->subCompile($node);

        return $this->code;
    }

    public function subCompile(NodeInterface $node): CompilerInterface
    {
        $node->compile($this);

        return $this;
    }

    public function writePhp(string ...$code): CompilerInterface
    {
        return $this->write('<?php ', implode($code), ' ?>');
    }

    public function write(string ...$code): CompilerInterface
    {
        $this->code .= implode('', $code);

        return $this;
    }

    public function indent(): CompilerInterface
    {
        $this->indentation += 4;

        return $this;
    }

    public function outdent(): CompilerInterface
    {
        if (4 <= $this->indentation) {
            $this->indentation -= 4;
        }

        return $this;
    }

    public function newLine(): CompilerInterface
    {
        return $this->write("\n", str_repeat(' ', $this->indentation));
    }
}
