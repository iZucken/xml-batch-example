<?php

namespace App\Features\ProductsImport\Services;

use App\Features\ImportLogs\DTO\ImportStats;
use App\Features\Products\VO\SIWeight;
use Doctrine\DBAL\Connection;

class ProductDataImporter
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function importBatchStreamFromGenerator(\Iterator $batches, callable $reportBatch): void
    {
        foreach ($batches as $batch) {
            $reportBatch($this->batch($batch));
        }
    }

    const MYSQL_ROWCOUNT_MEANS_INSERT = 1;
    const MYSQL_ROWCOUNT_MEANS_UPDATE = 2;

    function batch(array $records): ImportStats
    {
        $this->connection->beginTransaction();
        $stats = new ImportStats;
        $stats->discovered = count($records);
        $requiredAttributes = ['name', 'weight', 'category'];
        $stmt = $this->connection->prepare('INSERT DELAYED INTO products (name, weight, absolute_weight, category, description_common, description_for_ozon, description_for_wildberries)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE weight = VALUES(weight), absolute_weight = VALUES(absolute_weight), category = VALUES(category),
                                        description_common = VALUES(description_common),
                                        description_for_ozon = VALUES(description_for_ozon),
                                        description_for_wildberries = VALUES(description_for_wildberries)');
        foreach ($records as $record) {
            foreach ($requiredAttributes as $attribute) {
                if (empty($record[$attribute])) {
                    $stats->malformed++;
                    continue 2;
                }
            }
            try {
                $weight = new SIWeight($record['weight']);
            } catch (\InvalidArgumentException ) {
                $stats->malformed++;
                continue;
            }
            $result = $stmt->executeStatement([
                $record['name'],
                (string)$weight,
                $weight->toGrams(),
                $record['category'],
                $record['description_common'] ?? $record['description'] ?? null,
                $record['description_for_ozon'] ?? null,
                $record['description_for_wildberries'] ?? null,
            ]);
            if ($result === self::MYSQL_ROWCOUNT_MEANS_UPDATE) {
                $stats->updated++;
            } elseif ($result === self::MYSQL_ROWCOUNT_MEANS_INSERT) {
                $stats->created++;
            }
        }
        $this->connection->commit();
        return $stats;
    }
}
