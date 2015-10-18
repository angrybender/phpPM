<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 10.10.2015
 * Time: 14:11
 */

namespace Angrybender\Pattern;

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;

class Fabric
{
    public static function createAssign()
    {
        $mapper = new Mapper;
        $parseList = new ParserList(new Standard);
        $parseLine = new ParserLine(new Parser(new Lexer), new NodeVisitor, new NodeTraverser);
        $arrayHelper = new ArrayHelper;

        return new Assign($mapper, $parseList, $parseLine, $arrayHelper);
    }

    public static function createMatching()
    {
        return new Matching(new Mapper(), new ParserMethodCall());
    }
}