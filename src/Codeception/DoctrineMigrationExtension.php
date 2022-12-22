<?php

namespace App\Codeception;

use Codeception\Events;
use Codeception\Platform\Extension;

class DoctrineMigrationExtension extends Extension
{
    public static $events = [
        Events::SUITE_BEFORE => 'beforeSuite',
    ];

    public function beforeSuite()
    {
        try {
            $this->writeln('Running Doctrine Migrations...');
            exec('bin/console doctrine:migrations:migrate --prefix=App\\\\Migrations --no-interaction', $out, $code);
            assert($code === 0, "Migrations run with code $code");
        } catch (\Exception $e) {
            $this->writeln(
                sprintf(
                    'An error occurred whilst rebuilding the test database: %s',
                    $e->getMessage()
                )
            );
        }
    }
}
