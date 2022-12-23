<?php

namespace App\Features\BatchDataImport\Services;

class SimpleXmlElementBatcher
{
    public function getBatchGenerator(string $xmlSourceFilePathname, string $seekElement, int $batchSize, ?string $xmlSchemaFilePathname = null): \Generator
    {
        $xml = new \XMLReader;
        $xml->open($xmlSourceFilePathname);
        if ($xmlSchemaFilePathname) {
            $xml->setSchema($xmlSchemaFilePathname);
        }
        while ($xml->read() && $xml->name !== $seekElement) {
        }
        $i = 0;
        while ($xml->name === $seekElement) {
            $sxml = new \SimpleXMLElement($xml->readOuterXml());
            $map = [];
            foreach ($sxml->children() as $child) {
                $map[$child->getName()] = (string)$child;
            }
            $batch[] = $map;
            $i++;
            if ($i % $batchSize === 0) {
                yield $batch;
                $batch = [];
            }
            $xml->next($seekElement);
        }
        $xml->close();
        if (!empty($batch)) {
            yield $batch;
        }
    }
}
