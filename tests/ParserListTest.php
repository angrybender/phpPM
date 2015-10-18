<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 07.10.2015
 * Time: 19:48
 */

namespace Angrybender\Pattern\Tests;

use Angrybender\Pattern\ParserList;
use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;

class ParserListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProvider
     * @param string $code
     * @param array $pattern
     */
    public function testRun($code, $isNestedKeysName, array $pattern)
    {
        $code = '<?php ' . $code . ' = $foo;';

        $parser = new Parser(new Lexer);
        $prettyPrinter = new Standard;
        $parserList = new ParserList($prettyPrinter);

        $resutlPattern = $parserList->parse($parser->parse($code)[0], $isNestedKeysName);
        $this->assertEquals($pattern, $resutlPattern);
    }

    public function dataProvider()
    {
        return [
            [
                'list($id, $id_value)',
                false,
                ['id' => null, 'id_value' => null],
            ],
            [
                'list($id, $name, list($first, $second))',
                false,
                ['id' => null, 'name' => ['first' => null, 'second' => null]],
            ],
            [
                'list($id, $name, list($first, $second), $contacts, list($phone, $address))',
                false,
                ['id' => null, 'name' => ['first' => null, 'second' => null], 'contacts' => ['phone' => null, 'address' => null]],
            ],
            [
                'list($id, $contacts, list($phone, list($home, $work), $address))',
                false,
                ['id' => null, 'contacts' => ['phone' => ['home' => null, 'work' => null], 'address' => null]],
            ],

            [
                'list($id, $name, list($name_first, $name_second))',
                true,
                ['id' => null, 'name' => ['first' => null, 'second' => null]],
            ],
            [
                'list($id, $name, list($name_first, $name_second), $contacts, list($contacts_phone, $contacts_address))',
                true,
                ['id' => null, 'name' => ['first' => null, 'second' => null], 'contacts' => ['phone' => null, 'address' => null]],
            ],
            [
                'list($id, $contacts, list($contacts_phone, list($phone_home, $phone_work), $address))',
                true,
                ['id' => null, 'contacts' => ['phone' => ['home' => null, 'work' => null], 'address' => null]],
            ],
        ];
    }
}