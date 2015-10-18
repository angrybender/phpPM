<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 08.10.2015
 * Time: 20:22
 */

namespace Angrybender\Pattern\Tests;

use Angrybender\Pattern\ArrayHelper;
use Angrybender\Pattern\Assign;
use Angrybender\Pattern\Fabric;
use Angrybender\Pattern\Mapper;
use Angrybender\Pattern\ParserLine;
use Angrybender\Pattern\ParserList;
use PhpParser\Node;
use PhpParser\PrettyPrinter\Standard;


class AssignTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccess()
    {
        $pattern = [
            'id'    => null,
            'value' => null,
        ];
        $data = ['id' => 1, 'value' => 2];
        $node = $this->getMock(Node::class);
        $code = '<?php $foo = "bar";';

        $parserList = $this->getMock(ParserList::class, ['parse'], [], '', false);
        $parserList->expects($this->once())->method('parse')->with($this->equalTo($node))->will($this->returnValue($pattern));

        $parserLine = $this->getMock(ParserLine::class, ['parse'], [], '', false);
        $parserLine->expects($this->once())->method('parse')->will($this->returnValue($node));

        $mapper = $this->getMock(Mapper::class, ['match', 'current']);
        $mapper->expects($this->once())->method('match')->with($this->equalTo($pattern), $this->equalTo([$data]));
        $mapper->expects($this->once())->method('current')->will($this->returnValue($data));

        $assign = new Assign($mapper, $parserList, $parserLine, new ArrayHelper);

        $result = $assign->get($data);
        $this->assertEquals([1, 2], $result);
    }

    public function testFailure()
    {
        $pattern = [
            'id'    => null,
            'value' => null,
        ];
        $data = ['id' => 1, 'value' => 2];
        $node = $this->getMock(Node::class);
        $code = '<?php $foo = "bar";';

        $parserList = $this->getMock(ParserList::class, ['parse'], [], '', false);
        $parserList->expects($this->once())->method('parse')->with($this->equalTo($node))->will($this->returnValue($pattern));

        $parserLine = $this->getMock(ParserLine::class, ['parse'], [], '', false);
        $parserLine->expects($this->once())->method('parse')->will($this->returnValue($node));

        $mapper = $this->getMock(Mapper::class, ['match', 'current']);
        $mapper->expects($this->once())->method('match')->with($this->equalTo($pattern), $this->equalTo([$data]));
        $mapper->expects($this->once())->method('current')->will($this->returnValue(null));

        $assign = new Assign($mapper, $parserList, $parserLine, new ArrayHelper);

        $result = $assign->get($data);
        $this->assertEquals([null, null], $result);
    }

    public function testIntegration1()
    {
        $assign = Fabric::createAssign();

        list($id, $value) = $assign->get(['id' => 1, 'value' => 666]);

        $this->assertEquals(1, $id);
        $this->assertEquals(666, $value);
    }

    public function testIntegration2()
    {
        $assign = Fabric::createAssign();

        list(
            $id,
            $user, list(
                $name,
                $contacts
            )
        ) = $assign->get(['id' => 1,'user' => ['name'  => 'Foo Bar', 'contacts' => 'foo bar baz']]);

        $this->assertEquals(1, $id);
        $this->assertEquals('Foo Bar', $name);
        $this->assertEquals('foo bar baz', $contacts);
    }

    public function testIntegration3()
    {
        $assign = Fabric::createAssign();

        list(
            $id,
            $name,
            $user, list(
                $user_name,
                $user_contacts
            )
        ) = $assign->get(['id' => 1, 'name' => 'foo', 'user' => ['name'  => 'Foo Bar', 'contacts' => 'foo bar baz']], true);

        $this->assertEquals(1, $id);
        $this->assertEquals('foo', $name);
        $this->assertEquals('Foo Bar', $user_name);
        $this->assertEquals('foo bar baz', $user_contacts);
    }

    public function testIntegration4()
    {
        $assign = Fabric::createAssign();

        list(
            $id,
            $user, list(
                $namee,
                $contacts
            )
        ) = $assign->get(['id' => 1,'user' => ['name'  => 'Foo Bar', 'contacts' => 'foo bar baz']]);

        $this->assertEquals(null, $id);
        $this->assertEquals(null, $namee);
        $this->assertEquals(null, $contacts);
    }
}