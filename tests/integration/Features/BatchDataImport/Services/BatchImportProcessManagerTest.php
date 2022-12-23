<?php

namespace integration\Features\BatchDataImport\Services;

use App\Features\BatchDataImport\Services\BatchImportProcessManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @coversDefaultClass \App\Features\BatchDataImport\Services\BatchImportProcessManager
 */
class BatchImportProcessManagerTest extends TestCase
{
    /**
     * @covers ::startImportProcess
     */
    public function testStartImportProcess()
    {
        $sut = new BatchImportProcessManager();
        $pid = $sut->startImportProcess(new File(__DIR__ . '/../../../../_data/import_sample.xml'));
        $this->assertIsInt($pid);
    }
}
