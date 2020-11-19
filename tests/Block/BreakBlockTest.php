<?php

namespace YaFou\Visuel\Tests\Block;

use PHPUnit\Framework\TestCase;
use YaFou\Visuel\Node\Node;
use YaFou\Visuel\Node\PhpNode;
use YaFou\Visuel\Parser;
use YaFou\Visuel\Token;
use YaFou\Visuel\TokenStream;

class BreakBlockTest extends TestCase
{
    public function testParse()
    {
        $parser = new Parser();
        $node = $parser->parse(new TokenStream([new Token(Token::BLOCK, 'break')]));
        $this->assertEquals(new Node([new PhpNode('break;')]), $node);
    }
}
