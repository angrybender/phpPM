<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 12.10.2015
 * Time: 19:16
 */

namespace Angrybender\Pattern;

class ParserMethodCall
{
    /**
     * @param object $object
     * @param string $method
     * @return array|null
     * @throws \Exception
     */
    public function parse($object, $method)
    {
        $call = new \ReflectionMethod($object, $method);
        $params = $call->getParameters();
        $count = count($params);
        if ($count !== 1) {
            return null;
        }

        return $this->extractDefaultValue($params[0]);
    }

    /**
     * @param \ReflectionParameter $parameter
     * @return mixed|null
     */
    private function extractDefaultValue(\ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable()) {
            $value = $parameter->getDefaultValue();
        }
        else {
            return null;
        }

        if (empty($value) || !is_array($value)) {
            $value = null;
        }

        return $value;
    }
}