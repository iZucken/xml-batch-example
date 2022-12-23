<?php

namespace integration\Features\ProductsImport\Services;

use App\Features\ImportLogs\DTO\ImportStats;
use App\Features\ProductsImport\Services\ProductBatchPersist;
use Codeception\Test\Unit;
use Doctrine\DBAL\Connection;
use Pimcore\Tests\Helper\Pimcore;

/**
 * @coversDefaultClass \App\Features\ProductsImport\Services\ProductBatchPersist
 * @covers \App\Features\ProductsImport\Services\ProductBatchPersist
 */
class ProductBatchPersistTest extends Unit
{
    private ProductBatchPersist $sut;
    private Connection $db;

    public function setUp(): void
    {
        parent::setUp();
        /** @var Pimcore $pimcore */
        $pimcore = $this->getModule('\Pimcore\Tests\Helper\Pimcore');
        $this->db = $pimcore->grabService(Connection::class);
        $this->sut = new ProductBatchPersist($this->db);
    }

    /**
     * @covers ::importBatchStreamFromGenerator
     */
    public function testImportBatchStreamFromGenerator()
    {
        $all = [];
        $callable = function (ImportStats $stats) use (&$all) {
            $all[] = $stats;
        };
        $batches = [
            [
                ['name' => 'A', 'weight' => '1 kg', 'category' => 'A', 'description' => 'tldr;'],
                ['name' => 'B', 'weight' => '1 kg', 'category' => 'A', 'description' => 'tldr;'],
                ['name' => 'C', 'weight' => '1 kg', 'category' => 'B', 'description' => 'tldr;'],
                ['name' => 'D', 'weight' => '1 kg', 'category' => 'B', 'description' => 'tldr;'],
            ],
            [
                ['name' => 'A', 'weight' => '1 kg'],
                ['name' => 'B', 'category' => 'A'],
                ['weight' => '1 kg', 'category' => 'B'],
                ['name' => 'D', 'weight' => 'zzz', 'category' => 'B', 'description' => 'tldr;'],
            ],
            [
                ['name' => 'A', 'weight' => '1 kg', 'category' => 'A', 'description' => 'changed'],
                ['name' => 'B', 'weight' => '1 kg', 'category' => 'changed', 'description' => 'tldr;'],
                ['name' => 'C', 'weight' => '1 g', 'category' => 'B', 'description' => 'tldr;'],
            ],
            [
                ['name' => 'A', 'weight' => '1 kg', 'category' => 'A', 'description' => 'changed'],
                ['name' => 'B', 'weight' => '1 kg', 'category' => 'changed', 'description' => 'tldr;'],
                ['name' => 'C', 'weight' => '1 g', 'category' => 'B', 'description' => 'tldr;'],
            ],
        ];
        $this->sut->importBatchStreamFromGenerator(new \ArrayIterator($batches), $callable);
        $this->assertEquals([
            ImportStats::fromArray([
                'discovered' => 4,
                'created' => 4,
            ]),
            ImportStats::fromArray([
                'discovered' => 4,
                'malformed' => 4,
            ]),
            ImportStats::fromArray([
                'discovered' => 3,
                'updated' => 3,
            ]),
            ImportStats::fromArray([
                'discovered' => 3,
            ]),
        ], $all);
    }
}
