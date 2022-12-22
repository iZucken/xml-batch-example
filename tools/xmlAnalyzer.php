<?php

$reader = new \XMLReader;
$reader->open($argv[1]);
$tags = [];
$longestContent = 0;
while ($reader->read()) {
    if ($reader->nodeType !== \XMLReader::ELEMENT) {
        continue;
    }
    $tags[$reader->name] = ($tags[$reader->name] ?? [
        'occurrence' => 0,
        'maxSize' => 0,
        'empty' => 0,
    ]);
    $tags[$reader->name]['occurrence']++;
    $tags[$reader->name]['maxSize'] = max(strlen($reader->readOuterXml()), $tags[$reader->name]['maxSize']);
    $tags[$reader->name]['empty'] += empty($reader->value);
}
var_dump($tags);
