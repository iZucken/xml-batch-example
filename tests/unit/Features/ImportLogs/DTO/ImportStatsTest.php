<?php

namespace unit\Features\ImportLogs\DTO;

use App\Features\ImportLogs\DTO\ImportStats;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Features\ImportLogs\DTO\ImportStats
 */
class ImportStatsTest extends TestCase
{
    const VALID_ARRAY = [
        "discovered" => 10,
        "created" => 5,
        "updated" => 3,
        "malformed" => 2,
    ];
    const VALID_JSON = <<<JSON
{
    "discovered": 10,
    "created": 5,
    "updated": 3,
    "malformed": 2
}
JSON;

    /**
     * @covers ::fromArray
     */
    public function testFromArray()
    {
        $sut = ImportStats::fromArray(self::VALID_ARRAY);
        $this->assertEquals(10, $sut->discovered);
        $this->assertEquals(5, $sut->created);
        $this->assertEquals(3, $sut->updated);
        $this->assertEquals(2, $sut->malformed);
    }

    /**
     * @covers ::fromJson
     * @depends testFromArray
     */
    public function testFromJson()
    {
        $sut = ImportStats::fromJson(self::VALID_JSON);
        $this->assertEquals(10, $sut->discovered);
        $this->assertEquals(5, $sut->created);
        $this->assertEquals(3, $sut->updated);
        $this->assertEquals(2, $sut->malformed);
    }

    /**
     * @covers ::add
     */
    public function testAdd()
    {
        $sut = new ImportStats();
        $sut->add(ImportStats::fromJson(self::VALID_JSON));
        $sut->add(ImportStats::fromArray(self::VALID_ARRAY));
        $this->assertEquals(20, $sut->discovered);
        $this->assertEquals(10, $sut->created);
        $this->assertEquals(6, $sut->updated);
        $this->assertEquals(4, $sut->malformed);
    }
}
