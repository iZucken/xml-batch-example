<?php

namespace App\Features\BatchDataImport\Commands;

use App\Features\BatchDataImport\Exceptions\BatchInterruptException;
use App\Features\ImportLogs\DTO\ImportStats;
use App\Features\BatchDataImport\Services\SimpleXmlElementBatcher;
use App\Features\ImportLogs\Entities\ImportLog;
use App\Features\ImportLogs\Services\ImportLogRepository;
use App\Features\ProductsImport\Services\ProductDataImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'import:batch:simpleXmlProducts')]
class BatchImportCommand extends Command
{
    private ProductDataImporter $importer;
    private SimpleXmlElementBatcher $batcher;
    private ImportLogRepository $logRepository;
    private ImportLog $import;
    private int $shouldStopBySignal = 0;

    public function __construct(SimpleXmlElementBatcher $batcher, ImportLogRepository $logRepository, ProductDataImporter $importer)
    {
        parent::__construct();
        $this->importer = $importer;
        $this->batcher = $batcher;
        $this->logRepository = $logRepository;
    }

    protected function configure(): void
    {
        $this
            ->setName('import:batch:simpleXmlProducts')
            ->setDescription('Run batch XML products import.')
            ->addArgument('sourceFile', InputArgument::REQUIRED);
    }

    public function stopCommand(int $signal)
    {
        $this->shouldStopBySignal = $signal;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = new \SplFileInfo($input->getArgument('sourceFile'));
        pcntl_signal(SIGTERM, [$this, 'stopCommand']);
        pcntl_signal(SIGINT, [$this, 'stopCommand']);
        pcntl_signal(SIGHUP, [$this, 'stopCommand']);
        $generator = $this->batcher->getBatchGenerator(
            $file->getPathname(),
            'product',
            1000,
            __DIR__ . '/../../../../schemas/products_flexible.xsd',
        );
        $this->import = ImportLog::fromFileInfo($file);
        $this->logRepository->add($this->import);
        $reportStats = function (ImportStats $stats) use ($output) {
            pcntl_signal_dispatch();
            $this->import->updateStats($stats);
            $this->logRepository->update($this->import);
            foreach ((array)$this->import->getStats() as $stat => $value) {
                $output->writeln("$stat: $value");
            }
            if ($this->shouldStopBySignal) {
                throw new BatchInterruptException("Stopped by signal $this->shouldStopBySignal");
            }
        };
        try {
            $this->importer->importBatchStreamFromGenerator($generator, $reportStats);
            $this->import->complete();
        } catch (BatchInterruptException $exception) {
            $this->import->cancel($exception->getMessage());
        } catch (\Throwable $exception) {
            error_log($exception);
            $this->import->fail($exception->getMessage());
        }
        $this->logRepository->update($this->import);
        return 0;
    }
}
