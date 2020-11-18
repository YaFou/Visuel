<?php

namespace YaFou\Visuel;

use Exception;
use YaFou\Visuel\Loader\LoaderInterface;

class Renderer implements RendererInterface
{

    /**
     * @var LoaderInterface
     */
    private $loader;
    /**
     * @var LexerInterface|null
     */
    private $lexer;
    /**
     * @var ParserInterface|null
     */
    private $parser;
    /**
     * @var CompilerInterface|null
     */
    private $compiler;

    public function __construct(
        LoaderInterface $loader,
        LexerInterface $lexer = null,
        ParserInterface $parser = null,
        CompilerInterface $compiler = null
    )
    {
        $this->loader = $loader;
        $this->lexer = $lexer;
        $this->parser = $parser;
        $this->compiler = $compiler;
    }

    /**
     * @param string $name
     * @param array $parameters
     * @return string
     * @throws Exception
     */
    public function render(string $name, array $parameters = []): string
    {
        $source = $this->loader->load($name);

        if (!$this->lexer) {
            $this->lexer = new Lexer();
        }

        if (!$this->parser) {
            $this->parser = new Parser();
        }

        if (!$this->compiler) {
            $this->compiler = new Compiler();
        }

        $tokens = $this->lexer->tokenize($source);
        $node = $this->parser->parse($tokens);
        $__code = $this->compiler->compile($node);
        $__parameters = $parameters;

        return (static function () use ($__code, $__parameters) {
            ob_start();
            extract($__parameters);
            eval('?>' . $__code);

            return ob_get_clean();
        })();
    }
}
