<?php

include __DIR__ . '/../../../../autoload.php';
$assign = \Angrybender\Pattern\Fabric::createAssign();

$exampleSuccess = [
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

$exampleFail = [
    "type" =>  "document",
    "subject" =>  "My first document",
    "content" =>
        [
            "type" =>  "text/html",
            "text" =>  "<body><p>This is my document.</p></body>",
            "author" => [
                'name'    => 'Foo Bar',
                'id'      => 2,
            ]
        ]
];

function native(array $data)
{
    $type       = isset($data['type']) ? $data['type'] : null;
    $content_id = isset($data['content'], $data['content']['id']) ? $data['content']['id'] : null;
    $author_id  = isset($data['content'], $data['content']['author'], $data['content']['author']['id']) ? $data['content']['author']['id'] : null;
    $author_name= isset($data['content'], $data['content']['author'], $data['content']['author']['name']) ? $data['content']['author']['name'] : null;

    if (is_null($type) || is_null($content_id) || is_null($author_id) || is_null($author_name)) {
        return [null, null, null, null];
    }
    else {
        return [$type, $content_id, $author_id, $author_name];
    }
}

// native success 10000 times:
$start = microtime(true);
for ($i = 0; $i < 10000; $i++) {
    list($type, $content_id, $author_id, $author_name) = native($exampleSuccess);
}
$nativeSuccessTime = microtime(true) - $start;

// Assign:
$start = microtime(true);
for ($i = 0; $i < 10000; $i++) {
    list(
        $type,
        $content, list(
            $content_id,
            $content_author, list(
                $author_id,
                $author_name
            )
        )
    ) = $assign->get($exampleSuccess, true);
}
$assignSuccessTime = microtime(true) - $start;

echo 'Success % overhead: ', round(100*$assignSuccessTime/$nativeSuccessTime), '%', PHP_EOL;

// native fail 10000 times:
$start = microtime(true);
for ($i = 0; $i < 10000; $i++) {
    list($type, $content_id, $author_id, $author_name) = native($exampleFail);
}
$nativeFailTime = microtime(true) - $start;

// Assign:
$start = microtime(true);
for ($i = 0; $i < 10000; $i++) {
    list(
        $type,
        $content, list(
            $content_id,
            $content_author, list(
                $author_id,
                $author_name
            )
        )
    ) = $assign->get($exampleFail, true);
}
$assignFailTime = microtime(true) - $start;

echo 'Fail % overhead: ', round(100*$assignFailTime/$nativeFailTime), '%', PHP_EOL;