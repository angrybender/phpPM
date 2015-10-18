<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 14.10.2015
 * Time: 20:14
 */

include __DIR__ . '/../../../../autoload.php';

$assign = \Angrybender\Pattern\Fabric::createAssign();

$yaDisk = [
      "trash_size"  => 4631577437,
      "total_space" => 319975063552,
      "used_space"  => 26157681270,
      "system_folders"=>
          [
                "applications"  => "disk:/apps",
                "downloads"     => "disk:/download/"
          ]
];

list($system_folders, list(
    $applications,
    $downloads
)) = $assign->get($yaDisk);

var_dump($applications);
var_dump($downloads);