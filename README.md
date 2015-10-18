## Intro

В [некоторых](http://coffeescript.org/) [языках](http://es6-features.org/#ObjectMatchingShorthandNotation) есть такая штука как деструктивное (реструктуризующее) [присваивание](http://nl3.php.net/manual/en/function.list.php).

В общем случае, (на примере JS Harmony) реструктуризующее присваивание можно разделить на две категории:

- array matching: `[a, b] = ["foo", "bar"]`
- object matching: `{a, b} = {a : "foo", b: "bar"}`

В PHP есть первое `list()`, но нет второго.

Можно ли его добавить? Конечно, ничего не стоит написать обертку, которая будет принимать определенный паттерн по некоторому конвеншену и маппить хешмап на этот паттерн. Например так:

```PHP
// function _map(array $dataHash, array $patternHash);

$assigned = _map(['id' => 7, 'value' => 'foo bar', 'tags' => [1, 4, 6]], ["id" => 0, "value" => ""]);

// $assigned = ["id" => 7, "value" => "foo bar"] 
// (tags отчекрыжили)
```

А подсластить?

Хм... Оказывается `list()` может быть вложенным. Но он, черт возьми, не понимает хеши в том плане, что нет возможности как-то задать соответствие ключам. Да и вобще:

```
php > $ar = ['a' => 1, 'b' => 2];
php > list($x, $y) = $ar;
PHP Notice:  Undefined offset: 1 in php shell code on line 1
```

Но, `list()` вложенный, заставить понимать хешмапы его можно через обертку, а название ключей пусть соответствуют названиям переменных в левой части.

И вот тут мне карта пошла...

## Пс, пацан, не хочешь немного магии?

```PHP
list($id, $name) = $assign->get(['id' => 1, 'name' => 'Foo']);
var_dump($id); 		// $id = 1
var_dump($name); 	// $name = 'Foo'
```

**А вложенность?**

Не вопрос:

```PHP
list(
	$id, 
	$user, list(
		$name,
		$bio
	)
) = $assign->get([
	'id' => 1,
	'user' => [
		'name' => 'Foo',
		'bio' => 'baz bar'
	]
]);
```

**Что тут происходит?**

Есть у нас хешмап:

```PHP
$userData = [
	'id' => 1,
	'user' => [
		'name' => 'Foo',
		'bio' => 'baz bar'
	]
]
```

Соответствующий ему паттерн для деструктивного присваивания будет таков (такие):

- для извлечения только `id`: `list($id) = ...`
- для извлечения `user` и `id`: `list($id, $user) = ...`

Ну и для извлечения `name`, `bio` и всего что только в голову взбредет:

```PHP
list(
	$id, 			// 'id' => %some value%
	$user, list(	// 'user' => [ (ключевое слово list обозначает вложенность хешмапов)
		$name,		// 				['user']['name'] => 
		$bio		// 				['user']['bio'] => 
	)				//	]
)
```

Вложенность не ограничена.

**А что если структура не подойдет?**

Все переменные будут содержать волшебный `NULL`.

Допустим хешмап данных таков: `{id: 1, value: 'Foo bar'}`.

А паттерн таков: `list($id, $user)`.
 
Тогда, при извлечении, `$id` и `$user` будут `NULL`, т.к. ключа `user` в хешмапе нет.

**У меня во вложенном массиве тоже есть ключ id!**

Решаемо, только надо задать флаг:

```PHP
list(
	$id, 
	$user, list(
		$user_id,
		$user_name
	)
) = $assign->get([
	'id' => 1,
	'user' => [
		'id' => 7,
		'name' => 'Foo Bar'
	]
], true); // true - компоновать название родительского ключа с названиями ключей дочернего хешмапа (только до 1 уровня вложенности)
```

## А что дальше?

Ну, т.к. из маппинга данных на паттерн само собой получается matching, добавить гварды как в Erlang можно:

```PHP
class Pull {
	public function extractOnly($user = ['bio' => ['country' => 'RU']])
	{
		return $user;
	}
	
	public function allUsers()
	{
		return null;
	}
}

$list = [
	[
		'id' => 1,
		'bio' => ['name' => 'Jon', 'country' => 'US' ]
	],
	[
		'id' => 2,
		'bio' => ['name' => 'Ivan', 'country' => 'RU' ]
	],
];

$russians = array_filter( $matching->setObject(new Pull)->execute($list) );

var_export($russians)

// $russians = [['id' => 2, 'bio' => ['name' => 'Ivan', 'country' => 'RU']]];
```

Мы создаем пулл обработчиков (в виде объекта класса с публичными методами), каждый из методов данного класса может принимать один аргумент с default значением, и если оно array, то движок считает что это паттерн. В данном случае под этот паттерн будут подпадать только такие данные, внутри которых ключ bio.country будет равен 'RU'.

Не подходящие под этот паттерн данные будут подпадать в метод `allUsers`.

Как и в Erlang, если движок не найдет под очередную порцию данных подходящего метода, он бросит эксепшн. Тут это будет эксепшен `Angrybender\Pattern\Matching\NoMatch`.

Соответственно, кормить `$matching` надо массивом хешмапов. Он заменит каждый вложенный хешмап тем, что получится в результате работы одного из методов, под который попал очередной хешмап. Вызывается только один, первый попавшийся метод.

## Подробнее...

В папке `vendor/angrybender/pattern/Angrybender/examples` примеры. 

Например, в `match1.php` есть пример извлечения дат тех дней в которые идет дождь через публичное апи погодного сервиса.

Заодно там понятно откуда беруться объекты `$assign` и `$matching`.

## А пощупать?

Во-первых, взять через composer:

```JS
{
    "name": "",
    "description": "",
    "license": "MIT",
    "type": "project",
    "repositories": [
        {
            "url": "https://github.com/angrybender/phpPM.git",
            "type": "git"
        }
    ],
    "require": {
        "php": ">=5.3",
        "angrybender/pattern": "dev-master"
    }
}
```

**Я не использую composer**

Тогда придется поебаться:

- иди [сюда](https://github.com/nikic/PHP-Parser) и качай ветку 1.* куда удобно
- качай мою репу, опять же, куда душе угодно
- скрещивай этого ужа с ежом, чтобы грузились классы парсера, кури доку `nikic/PHP-Parser`-а для этого
- используй фабрику `\Angrybender\Pattern\Fabric`

**Как насчет DI**

DI-available, нужно только сделать сервис для `new PhpParser\Parser(new PhpParser\Lexer)`. 
Затем ты можешь инжектить `Angrybender\Pattern\Assign` и `Angrybender\Pattern\Matching` через свой любимый DI движок.

**achtung**

Разобранный паттерн кешируется, поэтому для `Angrybender\Pattern\Assign` и `Angrybender\Pattern\Matching` надо создавать новый инстанс для каждого отличающегося паттерна или пулла обработчиков!

## А тесты?

Все [по-взрослому](https://github.com/angrybender/phpPM/tree/master/tests).

## Производительность?

[Страдает](https://github.com/angrybender/phpPM/tree/master/Angrybender/benchmarks). В общем случае,
падение по сравнению с нативным кодом составляет, для присваивания, до 15 раз.