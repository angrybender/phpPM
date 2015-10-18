<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 07.10.2015
 * Time: 19:40
 */

namespace Angrybender\Pattern;

use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\Parser as PhpParserParser;
use PhpParser\PrettyPrinterAbstract;

class ParserList
{
    /**
     * @var PrettyPrinterAbstract
     */
    private $prettyPrinter;

    public function __construct(PrettyPrinterAbstract $prettyPrinter)
    {
        $this->prettyPrinter = $prettyPrinter;
    }

    /**
     * @param Node $stmts
     * @return Node\Expr\List_
     * @throws \Exception
     */
    private function getListPart(Node $stmts)
    {
        if ($stmts->getType() === 'Expr_Assign' && $stmts->var->getType() === 'Expr_List') {
            return $stmts->var;
        }
        elseif ($stmts->getType() === 'Expr_List') {
            return $stmts;
        }
        else {
            throw new \Exception("Unknown statement: " . $stmts->getType());
        }
    }

    /**
     * @param string            $name
     * @param boolean           $isNestedKeysName
     * @return string[]         [current, parent]
     */
    private function parseKeyName($name, $isNestedKeysName)
    {
        if (!$isNestedKeysName) {
            return [$name, ''];
        }
        else {
            $parts = explode('_', $name);
            if (count($parts) === 1) {
                return [$name, ''];
            }
            else {
                $parent = array_shift($parts);
                $current = join('_', $parts);
                return [$current, $parent];
            }
        }
    }

    private function recursiveStructParse(Node $stmts, $isNestedKeysName, $parentKey = null)
    {
        $pattern = [];
        $previousKey = null;
        /** @var Node $var */
        foreach ($stmts->vars as $var) {
            if ($var->getType() === 'Expr_Variable') {
                list($current, $parent) = $this->parseKeyName($var->name, $isNestedKeysName);
                $pattern[$current] = null;
                if ($isNestedKeysName && !is_null($parentKey)) {
                    $previousKey = $current;
                }
                else {
                    $previousKey = $var->name;
                }
            }
            elseif ($var->getType() === 'Expr_List' && !is_null($previousKey)) {
                $pattern[$previousKey] = $this->recursiveStructParse($var, $isNestedKeysName, $previousKey);
            }
            elseif ($var->getType() === 'Expr_List' && is_null($previousKey)) {
                throw new \Exception("Parsing list(): syntax error. Nested list() without key. Code: " . $this->prettyPrinter->prettyPrint($var));
            }
        }

        return $pattern;
    }

    /**
     * @param Node $code
     * @param bool $isNestedKeysName    true, if each pattern var-name includes parent key and child key (see tests)
     * @return array
     * @throws \Exception
     */
    public function parse(Node $code, $isNestedKeysName = false)
    {
        $stmts = $this->getListPart($code);

        return $this->recursiveStructParse($stmts, $isNestedKeysName);
    }
}