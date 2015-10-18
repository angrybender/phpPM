<?php

namespace Angrybender\Pattern\Tests;
use Angrybender\Pattern\Fabric;
use Angrybender\Pattern\Tests\Fixtures\MatchingTest1;
use Angrybender\Pattern\Tests\Fixtures\MatchingTest2;

if (!class_exists(MatchingTest1::class)) {
    include __DIR__ . '/Fixtures/MatchingTest1.php';
}

if (!class_exists(MatchingTest2::class)) {
    include __DIR__ . '/Fixtures/MatchingTest2.php';
}

/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 12.10.2015
 * Time: 20:52
 */
class MatchingTest extends \PHPUnit_Framework_TestCase
{
    public function testPatternsSuccess()
    {
        $data = [
            ['id' => 1, 'user' => ['name'  => 'foo1', 'country' => 'ru']],
            ['id' => 2, 'user' => ['name'  => 'foo2', 'country' => 'us']],
            ['id' => null],
            ['id' => 3, 'user' => ['name'  => 'foo3', 'country' => 'eu']],
            ['id' => 4, 'user' => ['name'  => 'foo4', 'country' => 'ru']],
        ];

        $matcher = Fabric::createMatching();
        $pull = new MatchingTest1();

        $result = $matcher->setObject($pull)->execute($data);

        $this->assertEquals([1, null, null, null, 4], $result);
    }

    /**
     * @expectedException \Angrybender\Pattern\Matching\NoMatch
     */
    public function testPatternsException()
    {
        $data = [
            ['id' => 1],
            ['id' => 2],
            ['id' => null],
            ['id' => 3],
            ['id' => 4],
        ];

        $matcher = Fabric::createMatching();
        $pull = new MatchingTest2();

        $result = $matcher->setObject($pull)->execute($data);
    }
}