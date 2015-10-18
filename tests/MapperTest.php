<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 07.10.2015
 * Time: 19:44
 */

namespace Angrybender\Pattern\Tests;

use Angrybender\Pattern\Mapper as Pattern;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $pattern = [
            'id'    => 0,
            'value' => '',
        ];

        $data = [
            [
                'id'    => 11,
                'value' => 'foo'
            ],
            [
                'id'    => '1',
                'value' => 'bar'
            ],
            [
                'id'    => 13,
                'value' => false,
            ],
            [
                'id'    => 13,
                'value' => [
                    'id'    => 0,
                    'value' => 'foo',
                ]
            ],
            [
                'id'    => 7,
                'value' => 'bar'
            ],
        ];

        $matching = new Pattern();
        $matching->match($pattern, $data);

        $this->assertEquals(
            $matching->current(),
            [
                'id'    => 11,
                'value' => 'foo'
            ]
        );
        $matching->next();
        $this->assertEquals(
            $matching->current(),
            [
                'id'    => 7,
                'value' => 'bar'
            ]
        );

        $this->assertEquals(2, iterator_count($matching));
    }

    public function testArrays()
    {
        $pattern = [
            'id'    => 0,
            'value' => [],
        ];

        $data = [
            [
                'id'    => 11,
                'value' => 'foo'
            ],
            [
                'id'    => '1',
                'value' => 'bar'
            ],
            [
                'id'    => 13,
                'value' => false,
            ],
            [
                'id'    => 13,
                'value' => [
                    'id'    => 0,
                    'value' => 'foo',
                ]
            ],
        ];

        $matching = new Pattern();
        $matching->match($pattern, $data);

        $this->assertEquals(
            $matching->current(),
            [
                'id'    => 13,
                'value' => [
                    'id'    => 0,
                    'value' => 'foo',
                ]
            ]
        );

        $this->assertEquals(1, iterator_count($matching));
    }

    public function testNested()
    {
        $pattern = [
            'id'    => 0,
            'value' => [
                'title' => '',
                'tags'  => [],
            ],
        ];

        $data = [
            [
                'id'    => 11,
                'value' => 'foo'
            ],
            [
                'id'    => '1',
                'value' => 'bar'
            ],
            [
                'id'    => 13,
                'value' => [
                    'title' => 'bar',
                    'tags'  => [1,2,3]
                ]
            ],
            [
                'id'    => 13,
                'value' => [
                    'id'    => 0,
                    'value' => 'foo',
                ]
            ]
        ];

        $matching = new Pattern();
        $matching->match($pattern, $data);

        $this->assertEquals(
            $matching->current(),
            [
                'id'    => 13,
                'value' => [
                    'title' => 'bar',
                    'tags'  => [1,2,3]
                ]
            ]
        );

        $this->assertEquals(1, iterator_count($matching));
    }

    public function testMatch()
    {
        $pattern = [
            'id'    => 0,
            'value' => [
                'title' => 'foo',
                'tags'  => [],
            ],
        ];

        $data = [
            [
                'id'    => 11,
                'value' => [
                    'title' => 'foo',
                    'tags'  => [1,2,3]
                ]
            ],
            [
                'id'    => 12,
                'value' => [
                    'title' => 'bar',
                    'tags'  => [1]
                ]
            ],
            [
                'id'    => 13,
                'value' => [
                    'title' => 'bar',
                    'tags'  => [1,2,5]
                ]
            ],
            [
                'id'    => 14,
                'value' => [
                    'title' => 'foo',
                    'tags'  => [1,3]
                ]
            ]
        ];

        $matching = new Pattern();
        $matching->match($pattern, $data);

        $this->assertEquals(2, iterator_count($matching));
        $matching->rewind();
        $id11 = $matching->current();
        $matching->next();
        $id14 = $matching->current();

        $this->assertEquals([
            'id'    => 11,
            'value' => [
                'title' => 'foo',
                'tags'  => [1,2,3]
            ]
        ], $id11);

        $this->assertEquals([
            'id'    => 14,
            'value' => [
                'title' => 'foo',
                'tags'  => [1,3]
            ]
        ], $id14);
    }
}