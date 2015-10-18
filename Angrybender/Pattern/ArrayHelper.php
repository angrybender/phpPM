<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 08.10.2015
 * Time: 20:45
 */

namespace Angrybender\Pattern;


class ArrayHelper
{
    private function isVector(array $array)
    {
        foreach ($array as $key => $t) {
            if (!is_int($key)) {
                return false;
            }
        }

        return true;
    }

    public function toTuple(array $hashMap, array $pattern)
    {
        $list = [];
        foreach ($pattern as $key => $value) {
            $list[] = $hashMap[$key];
            if (is_array($value)) {
                $list[] = $this->toTuple($hashMap[$key], $value);
            }
        }

        return $list;
    }
}