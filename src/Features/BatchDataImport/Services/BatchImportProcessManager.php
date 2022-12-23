<?php

namespace App\Features\BatchDataImport\Services;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Process\Process;

class BatchImportProcessManager
{
    public function startImportProcess(File $file)
    {
        $process = Process::fromShellCommandline("php bin/console import:batch:simpleXmlProducts {$file->getPathname()} &");
        $process->setWorkingDirectory(__DIR__ . '/../../../../');
        $process->setOptions(['create_new_console' => true]);
        $process->disableOutput();
        $process->setTimeout(0);
        $process->start();
    }
}
