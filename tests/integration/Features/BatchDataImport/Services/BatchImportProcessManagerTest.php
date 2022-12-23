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
        $tmp = tempnam('/tmp', 'batch-test');
        copy(codecept_data_dir() . '/import_sample.xml', $tmp);
        $this->assertFileExists($tmp);
        $pid = $sut->startImportProcess(new File($tmp));
        $this->assertIsInt($pid);
        sleep(1);
        $this->assertFileDoesNotExist($tmp);
    }
}
