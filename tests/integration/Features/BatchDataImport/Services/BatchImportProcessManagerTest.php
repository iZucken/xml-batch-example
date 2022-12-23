<?php

namespace integration\Features\BatchDataImport\Services;

use App\Features\BatchDataImport\Services\BatchImportProcessManager;
use Codeception\Test\Unit;
use Doctrine\DBAL\Connection;
use Pimcore\Tests\Helper\Pimcore;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @coversDefaultClass \App\Features\BatchDataImport\Services\BatchImportProcessManager
 */
class BatchImportProcessManagerTest extends Unit
{
    protected function tearDown(): void
    {
        parent::tearDown();
        /** @var Pimcore $pimcore */
        $pimcore = $this->getModule('\Pimcore\Tests\Helper\Pimcore');
        $db = $pimcore->grabService(Connection::class);
        $db->executeStatement("TRUNCATE TABLE import_logs");
        $db->executeStatement("TRUNCATE TABLE products");
    }

    /**
     * @covers ::startImportProcess
     */
    public function testStartImportProcess()
    {
        $sut = new BatchImportProcessManager();
        $tmp = tempnam('/tmp', 'batch-test');
        copy(codecept_data_dir() . '/import_sample.xml', $tmp);
        $this->assertFileExists($tmp);
        $pid = $sut->startImportProcess(new File($tmp));
        $this->assertIsInt($pid);
        sleep(1);
        $this->assertFileDoesNotExist($tmp);
    }
}
