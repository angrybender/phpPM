<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 12.10.2015
 * Time: 20:52
 */

namespace Angrybender\Pattern\Tests\Fixtures;


class MatchingTest1
{
    public function matchRu($pattern = ['user' => ['country' => 'ru']])
    {
        return $pattern['id'];
    }

    public function noRu($user)
    {
        return null;
    }
}