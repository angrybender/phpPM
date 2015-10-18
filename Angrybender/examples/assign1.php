<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 14.10.2015
 * Time: 20:14
 */

include __DIR__ . '/../../../../autoload.php';

$assign = \Angrybender\Pattern\Fabric::createAssign();

list($id, $name) = $assign->get(['id' => 1, 'name' => 'Foo']);
var_dump($id);
var_dump($name);

list($id, $name) = $assign->get(['id' => 1, 'user_name' => 'Foo']);
var_dump($id);
var_dump($name);