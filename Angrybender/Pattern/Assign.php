<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 07.10.2015
 * Time: 20:59
 */

namespace Angrybender\Pattern;


class Assign
{
    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @var ParserList
     */
    private $parserList;

    private $pattern = [];

    /**
     * @var ParserLine
     */
    private $parserLine;
    /**
     * @var ArrayHelper
     */
    private $arrayHelper;

    /**
     * cache empty result
     * @var array
     */
    private $emptyResultArray = [];

    /**
     * Assign constructor.
     * @param Mapper $mapper
     * @param ParserList $parserList
     * @param ParserLine $parserLine
     * @param ArrayHelper $arrayHelper
     */
    public function __construct(Mapper $mapper, ParserList $parserList, ParserLine $parserLine, ArrayHelper $arrayHelper)
    {
        $this->mapper = $mapper;
        $this->parserList = $parserList;
        $this->parserLine = $parserLine;
        $this->arrayHelper = $arrayHelper;
    }

    private function getPattern($isNestedKeysName)
    {
        if (!empty($this->pattern)) {
            return $this->pattern;
        }

        $trace = debug_backtrace();
        $trace = $trace[1];
        $code = file_get_contents($trace['file']);
        $line = $trace['line'];
        $stmts = $this->parserLine->parse($code, $line);
        $this->pattern = $this->parserList->parse($stmts, $isNestedKeysName);
        return $this->pattern;
    }

    private function getEmptyArray(array $pattern)
    {
        if (!empty($this->emptyResultArray)) {
            return $this->emptyResultArray;
        }

        $result = $this->arrayHelper->toTuple($pattern, $pattern);
        $result = array_fill(0, count($result), null);
        $this->emptyResultArray = $result;

        return $result;
    }

    public function get(array $hashMap, $isNestedKeysName = false)
    {
        $pattern = $this->getPattern($isNestedKeysName);
        $this->mapper->match($pattern, [$hashMap]);
        $data = $this->mapper->current();

        if (empty($data)) {
            $result = $this->getEmptyArray($pattern);
        }
        else {
            $result = $this->arrayHelper->toTuple($data, $pattern);
        }

        return $result;
    }
}