<?php

declare(strict_types=1);

namespace App\DataDefinitions\Providers;

use Wvision\Bundle\DataDefinitionsBundle\Filter\FilterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Provider\ImportDataSetInterface;
use Wvision\Bundle\DataDefinitionsBundle\Provider\XmlProvider;
use XMLElementIterator;
use XMLElementXpathFilter;
use XMLReader;
use XMLReaderNode;

class StreamingXmlProvider extends XmlProvider
{
    public function getData(
        array $configuration,
        ImportDefinitionInterface $definition,
        array $params,
        FilterInterface $filter = null
    ): ImportDataSetInterface {
        $reader = new XMLReader();
        $reader->open($this->getFile($params));
        return new IteratorProcessorDataset(
            new XMLElementXpathFilter(
                new XMLElementIterator($reader),
                $configuration['xPath']),
            fn (XMLReaderNode $node) => (array)$node->getSimpleXMLElement());
    }
}
