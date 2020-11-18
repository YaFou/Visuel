<?php

namespace YaFou\Visuel;

use Exception;
use YaFou\Visuel\Cache\CacheInterface;
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
    /**
     * @var CacheInterface|null
     */
    private $cache;

    public function __construct(
        LoaderInterface $loader,
        CacheInterface $cache = null,
        LexerInterface $lexer = null,
        ParserInterface $parser = null,
        CompilerInterface $compiler = null
    )
    {
        $this->loader = $loader;
        $this->cache = $cache;
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

        if ($this->cache && $this->cache->has($source)) {
            $__code = $this->cache->get($source);
        } else {
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

            if ($this->cache) {
                $this->cache->set($source, $__code);
            }
        }

        $__parameters = $parameters;

        return (static function () use ($__code, $__parameters) {
            ob_start();
            extract($__parameters);
            eval('?>' . $__code);

            return ob_get_clean();
        })();
    }
}
