<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 07.10.2015
 * Time: 21:23
 */

namespace Angrybender\Pattern\Tests;

use Angrybender\Pattern\NodeVisitor;
use Angrybender\Pattern\ParserLine;
use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;

class ParserLineTest extends \PHPUnit_Framework_TestCase
{
    public function testParse1()
    {
        $parser = new Parser(new Lexer);
        $parserLine = new ParserLine($parser, new NodeVisitor, new NodeTraverser);

        $code = '<?php

            class Test {

                private $field = null;

                public function findMe()
                {
                    $noLine = 1;
                    $line = "line"; // line 10
                }
            }
        ';

        $stmts = $parserLine->parse($code, 10);

        $this->assertInstanceOf(Node::class, $stmts);
        $this->assertEquals('Expr_Assign', $stmts->getType());
        $this->assertEquals('line', $stmts->var->name);
    }

    public function testParse2()
    {
        $parser = new Parser(new Lexer);
        $parserLine = new ParserLine($parser, new NodeVisitor, new NodeTraverser);

        $code = '<?php

            class Test {

                private $field = null;

                public function findMe()
                {
                    $noLine = 1;
                    $line
                            =
                                    "line"; // line 12
                }
            }
        ';

        $stmts = $parserLine->parse($code, 12);

        $this->assertInstanceOf(Node::class, $stmts);
        $this->assertEquals('Expr_Assign', $stmts->getType());
        $this->assertEquals('line', $stmts->var->name);
    }

    public function testParse3()
    {
        $parser = new Parser(new Lexer);
        $parserLine = new ParserLine($parser, new NodeVisitor, new NodeTraverser);

        $code = '<?php

            class Test {

                private $field = null;

                public function findMe()
                {
                    $noLine = 1; // line 9
                    list(
                        $id,
                        $name, list(
                                    $first,
                                    $second
                               )
                    ) = $object->get(); // 16
                }
            }
        ';

        $stmts = $parserLine->parse($code, 16);

        $this->assertInstanceOf(Node::class, $stmts);
        $this->assertEquals('Expr_Assign', $stmts->getType());
        $this->assertEquals('Expr_List', $stmts->var->getType());
    }
}