<?php

namespace App\Features\ImportLogs\Services;

use App\Features\ImportLogs\Entities\ImportLog;
use Doctrine\DBAL\Connection;
use Symfony\Component\Serializer\SerializerInterface;

class ImportLogRepository
{
    private Connection $db;
    private SerializerInterface $serializer;

    public function __construct(Connection $db, SerializerInterface $serializer)
    {
        $this->db = $db;
        $this->serializer = $serializer;
    }

    public function add(ImportLog $import): void
    {
        $this->db->insert('import_logs', [
            'uuid' => $import->getUuid(),
            'started_at' => $import->getStartedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $import->getUpdatedAt()->format('Y-m-d H:i:s'),
            'state' => $import->getState(),
            'source_filename' => $import->getSourceFilename(),
            'status_message' => $import->getStatusMessage(),
            'stats' => $this->serializer->serialize($import->getStats(), 'json'),
        ]);
    }

    public function update(ImportLog $import): void
    {
        $this->db->update('import_logs', [
            'updated_at' => $import->getUpdatedAt()->format('Y-m-d H:i:s'),
            'state' => $import->getState(),
            'stats' => $this->serializer->serialize($import->getStats(), 'json'),
            'status_message' => $import->getStatusMessage(),
        ], [
            'uuid' => $import->getUuid(),
        ]);
    }

    public function getTotalLogs(): int
    {
        return $this->db->executeQuery('SELECT COUNT(uuid) FROM import_logs')->fetchOne();
    }

    public function getTop(): array
    {
        return array_map(
            [ImportLog::class, 'fromDbRow'],
            $this->db->executeQuery('SELECT * FROM import_logs ORDER BY started_at DESC LIMIT 10')
                ->fetchAllAssociative()
        );
    }

    public function getByUuid(string $uuid): ?ImportLog
    {
        if ($row = $this->db->executeQuery("SELECT * FROM import_logs WHERE uuid = ?", [$uuid])->fetchAssociative()) {
            return ImportLog::fromDbRow($row);
        }
        return null;
    }
}
