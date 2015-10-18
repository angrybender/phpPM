<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 08.10.2015
 * Time: 20:46
 */

namespace Angrybender\Pattern\Tests;


use Angrybender\Pattern\ArrayHelper;

class ArrayHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerRun
     * @param array $hashMap
     * @param array $pattern
     * @param array $result
     */
    public function testRun(array $hashMap, array $pattern, array $result)
    {
        $helper = new ArrayHelper;
        $this->assertEquals($result, $helper->toTuple($hashMap, $pattern));
    }

    public function providerRun()
    {
        return [
            [
                [
                    'id'    => 11,
                    'value' => 'foo'
                ],
                [
                    'id'    => null,
                    'value' => null,
                ],
                [11, 'foo']
            ],
            [
                [
                    'id'    => 13,
                    'value' => ['id' => 0, 'value' => 'foo' ]
                ],
                [
                    'id'    => null,
                    'value' => ['id' => null, 'value' => null]
                ],
                [13, ['id' => 0, 'value' => 'foo' ], [0, 'foo']]
            ],
            [
                [
                    'id'    => 13,
                    'value' => ['title' => 'bar', 'tags'  => [1,2,3]]
                ],
                [
                    'id'    => null,
                    'value' => ['title' => null, 'tags'  => null]
                ],
                [13, ['title' => 'bar', 'tags'  => [1,2,3]], ['bar', [1,2,3]]]
            ],
            [
                ['id' => 13, 'name' => ['first' => 'foo', 'second' => 'bar'], 'contacts' => ['phone' => '7809', 'address' => 'foo bar']],
                ['id' => null, 'name' => ['first' => null, 'second' => null], 'contacts' => ['phone' => null, 'address' => null]],
                [13, ['first' => 'foo', 'second' => 'bar'], ['foo', 'bar'], ['phone' => '7809', 'address' => 'foo bar'], ['7809', 'foo bar']]
            ]
        ];
    }
}