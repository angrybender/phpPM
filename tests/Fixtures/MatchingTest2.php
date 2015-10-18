<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 12.10.2015
 * Time: 20:52
 */

namespace Angrybender\Pattern\Tests\Fixtures;


class MatchingTest2
{
    public function match($pattern = ['user' => null])
    {
        return $pattern['user'];
    }
}