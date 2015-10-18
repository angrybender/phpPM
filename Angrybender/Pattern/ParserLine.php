<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 07.10.2015
 * Time: 21:05
 */

namespace Angrybender\Pattern;

use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\Parser as PhpParserParser;
use PhpParser\PrettyPrinterAbstract;

class ParserLine
{
    /**
     * @var PhpParserParser
     */
    private $parser;
    /**
     * @var NodeVisitor
     */
    private $nodeVisitor;
    /**
     * @var NodeTraverser
     */
    private $nodeTraverser;

    /**
     * Parser constructor.
     * @param PhpParserParser $parser
     * @param NodeVisitor $nodeVisitor
     * @param NodeTraverser $nodeTraverser
     */
    public function __construct(PhpParserParser $parser, NodeVisitor $nodeVisitor, NodeTraverser $nodeTraverser)
    {
        $this->parser = $parser;
        $this->nodeVisitor = $nodeVisitor;
        $this->nodeTraverser = $nodeTraverser;
    }

    /**
     * @param string $code
     * @param int $lineNumber
     * @return Node
     */
    public function parse($code, $lineNumber)
    {
        $stmts = $this->parser->parse($code);
        $this->nodeVisitor->setLine($lineNumber);

        $this->nodeTraverser->addVisitor($this->nodeVisitor);
        $this->nodeTraverser->traverse($stmts);

        $node = $this->nodeVisitor->getNode();
        return $node;
    }
}