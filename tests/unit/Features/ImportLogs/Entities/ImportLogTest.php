<?php

namespace unit\Features\ImportLogs\Entities;

use App\Features\ImportLogs\DTO\ImportStats;
use App\Features\ImportLogs\Entities\ImportLog;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

/**
 * @coversDefaultClass \App\Features\ImportLogs\Entities\ImportLog
 * @covers \App\Features\ImportLogs\Entities\ImportLog
 */
class ImportLogTest extends TestCase
{
    /**
     * @covers ::fromFileInfo
     */
    public function testFromFileInfo()
    {
        $sut = self::newFromSplFileInfo();
        $this->assertEquals("import_sample.xml", $sut->getSourceFilename());
        $this->assertEquals(ImportLog::STATE_PROGRESS, $sut->getState());
        $this->assertEquals('Progress', $sut->getStatusMessage());
    }

    /**
     * @covers ::fromDbRow
     */
    public function testFromDbRow()
    {
        $uuid = Uuid::v4();
        $sut = ImportLog::fromDbRow([
            'uuid' => $uuid,
            'started_at' => '2022-12-23 05:25:54',
            'updated_at' => '2022-12-23 07:25:54',
            'state' => (string)ImportLog::STATE_PROGRESS,
            'status_message' => 'Progress',
            'source_filename' => "import_sample.xml",
            'stats' => '{"discovered":10}',
        ]);
        $this->assertEquals($uuid, $sut->getUuid());
        $this->assertEquals('2022-12-23 05:25:54', $sut->getStartedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals('2022-12-23 07:25:54',$sut->getUpdatedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals(ImportStats::fromArray([
            'discovered' => 10,
        ]), $sut->getStats());
    }

    /**
     * @covers ::updateStats
     */
    public function testUpdateStats()
    {
        $sut = self::newFromSplFileInfo();
        $stats = ImportStats::fromArray([
            'discovered' => 10,
        ]);
        $sut->updateStats($stats);
        $this->assertEquals(ImportLog::STATE_PROGRESS, $sut->getState());
        $this->assertEquals("Progress", $sut->getStatusMessage());
        $this->assertEquals($stats, $sut->getStats());
        $sut->updateStats($stats);
        $this->assertEquals(ImportStats::fromArray([
            'discovered' => 20,
        ]), $sut->getStats());
    }

    /**
     * @covers ::complete
     */
    public function testComplete()
    {
        $sut = self::newFromSplFileInfo();
        $sut->complete();
        $this->assertEquals(ImportLog::STATE_COMPLETE, $sut->getState());
        $this->assertEquals("Ok", $sut->getStatusMessage());
    }

    /**
     * @covers ::fail
     */
    public function testFail()
    {
        $sut = self::newFromSplFileInfo();
        $sut->fail("Test fail");
        $this->assertEquals(ImportLog::STATE_ERROR, $sut->getState());
        $this->assertEquals("Test fail", $sut->getStatusMessage());
    }

    /**
     * @covers ::cancel
     */
    public function testCancel()
    {
        $sut = self::newFromSplFileInfo();
        $sut->cancel("Test cancel");
        $this->assertEquals(ImportLog::STATE_CANCELLED, $sut->getState());
        $this->assertEquals("Test cancel", $sut->getStatusMessage());
    }

    private static function newFromSplFileInfo(): ImportLog
    {
        return ImportLog::fromFileInfo(
            new \SplFileInfo(codecept_data_dir() . "/import_sample.xml"));
    }
}
