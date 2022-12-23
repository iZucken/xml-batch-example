<?php

namespace integration\Features\BatchDataImport\Commands;

use App\Features\BatchDataImport\Commands\BatchImportCommand;
use Codeception\Test\Unit;
use Pimcore\Console\Application;
use Pimcore\Tests\Helper\Pimcore;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass \App\Features\BatchDataImport\Commands\BatchImportCommand
 */
class BatchImportCommandTest extends Unit
{
    private BatchImportCommand $command;

    public function setUp(): void
    {
        parent::setUp();
        /** @var Pimcore $pimcore */
        $pimcore = $this->getModule('\Pimcore\Tests\Helper\Pimcore');
        $this->command = (new Application($pimcore->getKernel()))->find('import:batch:simpleXmlProducts');
    }

    /**
     * @covers ::__construct
     * @covers ::configure
     * @covers ::execute
     */
    public function testExecute()
    {
        $tester = new CommandTester($this->command);
        $tmp = tempnam("/tmp", "batch-test-");
        copy(codecept_data_dir() . "/import_sample.xml", $tmp);
        $tester->execute([
            'sourceFile' => $tmp,
        ]);
        $tester->assertCommandIsSuccessful();
        $this->assertFileExists($tmp);
        $tester->execute([
            'sourceFile' => $tmp,
            '--removeSources' => true,
        ]);
        $tester->assertCommandIsSuccessful();
        $this->assertFileDoesNotExist($tmp);
        copy(codecept_data_dir() . "/import_sample_broken.xml", $tmp);
        $tester->execute([
            'sourceFile' => $tmp,
            '--removeSources' => true,
        ]);
        $this->assertEquals(1, $tester->getStatusCode());
        copy(codecept_data_dir() . "/import_sample.xml", $tmp);
        $this->command->stopCommand(SIGHUP);
        $tester->execute([
            'sourceFile' => $tmp,
            '--batchSize' => 1,
            '--removeSources' => true,
        ]);
        $this->assertStringContainsString('Stopped by signal [1]', $tester->getDisplay());
        $tester->assertCommandIsSuccessful();
    }
}
