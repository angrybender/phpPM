<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 14.10.2015
 * Time: 20:14
 */

include __DIR__ . '/../../vendor/autoload.php';

$assign = \Angrybender\Pattern\Fabric::createAssign();

$example = [
      "type" =>  "document",
      "subject" =>  "My first document",
      "content" =>
      [
          'id'  => 7,
          "type" =>  "text/html",
          "text" =>  "<body><p>This is my document.</p></body>",
          "author" => [
              'name'    => 'Foo Bar',
              'id'      => 2,
          ]
      ]
];

list(
    $type,
    $content, list(
        $content_id,
        $content_author, list(
            $author_id,
            $author_name
        )
    )
) = $assign->get($example, true);

var_dump($type);
var_dump($content_id);
var_dump($author_id);
var_dump($author_name);