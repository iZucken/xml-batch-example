<?php

namespace integration\Features\ImportLogs\Services;

use App\Features\ImportLogs\DTO\ImportStats;
use App\Features\ImportLogs\Entities\ImportLog;
use App\Features\ImportLogs\Services\ImportLogRepository;
use Codeception\Test\Unit;
use Doctrine\DBAL\Connection;
use Pimcore\Tests\Helper\Pimcore;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @coversDefaultClass \App\Features\ImportLogs\Services\ImportLogRepository
 */
class ImportLogRepositoryTest extends Unit
{
    private Connection $db;
    private ImportLogRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        /** @var Pimcore $pimcore */
        $pimcore = $this->getModule('\Pimcore\Tests\Helper\Pimcore');
        $this->db = $pimcore->grabService(Connection::class);
        $this->repository = new ImportLogRepository(
            $this->db,
            $pimcore->grabService(SerializerInterface::class),
        );
    }

    /**
     * @covers ::__construct
     * @covers ::add
     */
    public function testAdd()
    {
        $import = ImportLog::fromFileInfo(new \SplFileInfo(codecept_data_dir() . "/import_sample.xml"));
        $this->repository->add($import);
        return $import;
    }

    /**
     * @covers ::getByUuid
     * @depends testAdd
     */
    public function testGetByUuid(ImportLog $import)
    {
        $this->assertNull($this->repository->getByUuid(Uuid::v4()));
        $retrieved = $this->repository->getByUuid($import->getUuid());
        $this->assertEquals($import->getSourceFilename(), $retrieved->getSourceFilename());
        $this->assertEquals($import->getState(), $retrieved->getState());
        $this->assertEquals($import->getStatusMessage(), $retrieved->getStatusMessage());
        $this->assertEquals($import->getStats(), $retrieved->getStats());
        return $import;
    }

    /**
     * @covers ::update
     * @depends testGetByUuid
     */
    public function testUpdate(ImportLog $import)
    {
        $toUpdate = $this->repository->getByUuid($import->getUuid());
        $toUpdate->updateStats(ImportStats::fromArray([
            'discovered' => 10,
        ]));
        $toUpdate->complete();
        $this->repository->update($toUpdate);
        $toUpdate = $this->repository->getByUuid($import->getUuid());
        $this->assertEquals($import->getSourceFilename(), $toUpdate->getSourceFilename());
        $this->assertNotEquals($import->getState(), $toUpdate->getState());
        $this->assertNotEquals($import->getStatusMessage(), $toUpdate->getStatusMessage());
        $this->assertNotEquals($import->getStats(), $toUpdate->getStats());
        return $import;
    }

    /**
     * @covers ::getTotalLogs
     * @depends testUpdate
     */
    public function testGetTotalLogs()
    {
        $this->assertEquals(1, $this->repository->getTotalLogs());
    }

    /**
     * @covers ::getTop
     * @depends testUpdate
     */
    public function testGetTop(ImportLog $import)
    {
        $this->assertEquals($import->getSourceFilename(), $this->repository->getTop()[0]->getSourceFilename());
        $this->assertEquals($import->getStartedAt()->format(DATE_ATOM), $this->repository->getTop()[0]->getStartedAt()->format(DATE_ATOM));
    }
}
