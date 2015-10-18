<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 14.10.2015
 * Time: 20:35
 */

namespace Angrybender\Pattern\Examples;

include __DIR__ . '/../../vendor/autoload.php';

$matching = \Angrybender\Pattern\Fabric::createMatching();
$assign = \Angrybender\Pattern\Fabric::createAssign();

class Pull {

    public function selectRainDays($forecast = ['weather' => [0 => ['main' => 'Rain']]])
    {
        return date('Y-m-d', strtotime($forecast['dt_txt']));
    }

    public function allDays()
    {
        return null;
    }
}
// extract Rain days from http://api.openweathermap.org/ forecast (see api help: http://openweathermap.org/forecast5)
$weather = json_decode(file_get_contents(__DIR__ . '/openweathermap.json'), true);

list($city, $list) = $assign->get($weather);

$rainDays = $matching->setObject(new Pull)->execute($list);
$rainDays = array_unique(array_filter($rainDays));
var_export($rainDays);