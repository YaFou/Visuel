<?php

namespace YaFou\Visuel\Block;

use YaFou\Visuel\Exception\ParseException;
use YaFou\Visuel\Node\CaseNode;
use YaFou\Visuel\Node\DefaultNode;
use YaFou\Visuel\Node\Node;
use YaFou\Visuel\Node\NodeInterface;
use YaFou\Visuel\Node\SwitchNode;
use YaFou\Visuel\Parser;

class SwitchBlock extends AbstractBlock
{

    public function getName(): string
    {
        return 'switch';
    }

    /**
     * @param Parser $parser
     * @param string|null $arguments
     * @return NodeInterface
     * @throws ParseException
     */
    public function parse(Parser $parser, string $arguments = null): NodeInterface
    {
        $parser->waitUntilBlock('case', 'endswitch', 'default');
        $children = [];
        $token = $parser->getStream()->getToken();
        $defaultPresent = false;

        while ('endswitch' !== $token->getValue()) {
            if ('case' === $token->getValue()) {
                $caseValue = $parser->parseBlock(true);

                $caseChildren = $defaultPresent ?
                    $parser->waitUntilBlock('case', 'endswitch') :
                    $parser->waitUntilBlock('case', 'endswitch', 'default');

                $children[] = new CaseNode($caseValue, $caseChildren);
                $token = $parser->getStream()->getToken();

                continue;
            }

            $defaultPresent = true;
            $parser->parseBlock();
            $children[] = new DefaultNode($parser->waitUntilBlock('case', 'endswitch'));
            $token = $parser->getStream()->getToken();
        }

        $parser->parseBlock();

        return new SwitchNode($arguments, new Node($children));
    }

    public function expectArguments(): bool
    {
        return true;
    }
}
