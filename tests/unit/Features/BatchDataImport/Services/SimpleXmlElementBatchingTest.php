<?php

namespace unit\Features\BatchDataImport\Services;

use App\Features\BatchDataImport\Services\SimpleXmlElementBatching;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Features\BatchDataImport\Services\SimpleXmlElementBatching
 */
class SimpleXmlElementBatchingTest extends TestCase
{
    /**
     * @covers ::getBatchGenerator
     */
    public function testGetBatchGeneratorNice()
    {
        $sut = new SimpleXmlElementBatching();
        $batches = iterator_to_array($sut->getBatchGenerator(
            codecept_data_dir() . "/import_sample.xml",
            'product',
            5,
            codecept_root_dir() . "/schemas/products_flexible.xsd",
        ));
        $this->assertCount(2, $batches);
        $this->assertCount(5, $batches[0]);
        $this->assertCount(5, $batches[1]);
        $this->assertEquals('in suscipit', $batches[0][0]['name']);
        $this->assertEquals('id accusantium', $batches[1][0]['name']);
        $batches = iterator_to_array($sut->getBatchGenerator(
            codecept_data_dir() . "/import_sample.xml",
            'product',
            3,
            codecept_root_dir() . "/schemas/products_flexible.xsd",
        ));
        $this->assertCount(4, $batches);
        $this->assertCount(3, $batches[0]);
        $this->assertEquals('in suscipit', $batches[0][0]['name']);
        $this->assertEquals('autem molestiae', $batches[1][0]['name']);
    }

    /**
     * @covers ::getBatchGenerator
     * @depends testGetBatchGeneratorNice
     */
    public function testGetBatchGeneratorBroken()
    {
        $sut = new SimpleXmlElementBatching();
        $this->expectWarning();
        $batches = iterator_to_array($sut->getBatchGenerator(
            codecept_data_dir() . "/import_sample_broken.xml",
            'product',
            5,
        ));
        $this->assertCount(2, $batches);
        $this->assertCount(5, $batches[0]);
        $this->expectError();
        iterator_to_array($sut->getBatchGenerator(
            codecept_data_dir() . "/import_sample_broken.xml",
            'product',
            5,
            codecept_root_dir() . "/schemas/products_flexible.xsd",
        ));
    }
}
