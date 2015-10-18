<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 12.10.2015
 * Time: 19:03
 */

namespace Angrybender\Pattern;

use Angrybender\Pattern\Matching\NoMatch;

class Matching
{
    /**
     * @var Mapper
     */
    private $mapper;
    /**
     * @var ParserMethodCall
     */
    private $parserMethodCall;

    private $patterns = [];

    /**
     * @var array
     */
    private $flow = [];

    /**
     * Matching constructor.
     * @param Mapper $mapper
     * @param ParserMethodCall $parserMethodCall
     */
    public function __construct(Mapper $mapper, ParserMethodCall $parserMethodCall)
    {
        $this->mapper = $mapper;
        $this->parserMethodCall = $parserMethodCall;
    }

    private function setPattern($object, $methodName)
    {
        $cacheKey = get_class($object) . '::' . $methodName;
        if (!array_key_exists($cacheKey, $this->patterns)) {
            $this->patterns[$cacheKey] = $this->parserMethodCall->parse($object, $methodName);
        }

        $this->flow[$cacheKey] = [$object, $methodName];
    }

    private function matchValue(array $value)
    {
        $isCalled = false;
        foreach ($this->patterns as $key => $pattern) {
            list($object, $method) = $this->flow[$key];
            if (empty($pattern)) {
                $value = $object->$method($value);
                $isCalled = true;
                break;
            }
            else {
                try {
                    $this->mapper->match($pattern, [$value]);
                }
                catch (\Exception $e) {
                    $isCalled = false;
                    continue;
                }

                $mappedValue = $this->mapper->current();
                if (!is_null($mappedValue)) {
                    $isCalled = true;
                    $value = $object->$method($value);
                    break;
                }
            }
        }

        if (!$isCalled) {
            throw new NoMatch;
        }

        return $value;
    }

    /**
     * @param object $object
     * @return self
     */
    public function setObject($object)
    {
        foreach (get_class_methods($object) as $methodName) {
            $this->setPattern($object, $methodName);
        }
        return $this;
    }

    public function execute(array $data)
    {
        foreach ($data as $i => $value) {
            $data[$i] = $this->matchValue($value);
        }

        return $data;
    }
}