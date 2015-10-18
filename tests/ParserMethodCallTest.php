<?php

namespace Angrybender\Pattern\Tests;
use Angrybender\Pattern\Mapper;
use Angrybender\Pattern\ParserMethodCall;

if (!class_exists(Fixtures\ParserMethodCallTest::class)) {
    include __DIR__ . '/Fixtures/ParserMethodCallTest.php';
}

/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 12.10.2015
 * Time: 19:17
 */
class ParserMethodCallTest extends \PHPUnit_Framework_TestCase
{
    public function testPatternMatching()
    {
        $matching = new ParserMethodCall(new Mapper());
        $arguments = $matching->parse(new Fixtures\ParserMethodCallTest(), 'method1');

        $this->assertEquals(['id' => null, 'value' => ['id' => null, 'tags' => null]], $arguments);
    }

    public function testNull1()
    {
        $matching = new ParserMethodCall(new Mapper());
        $arguments = $matching->parse(new Fixtures\ParserMethodCallTest(), 'method4');

        $this->assertEquals(null, $arguments);
    }

    public function testNull2()
    {
        $matching = new ParserMethodCall(new Mapper());
        $arguments = $matching->parse(new Fixtures\ParserMethodCallTest(), 'method5');

        $this->assertEquals(null, $arguments);
    }
}