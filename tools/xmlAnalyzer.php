<?php

$reader = new \XMLReader;
$reader->open($argv[1]);
$tags = [];
$longestContent = 0;
while ($reader->read()) {
    $tags[$reader->name] = ($tags[$reader->name] ?? 0) + 1;
    $longestContent = max(strlen($reader->value), $longestContent);
}
var_dump([
    'tags' => $tags,
    'longestContent' => $longestContent,
]);
