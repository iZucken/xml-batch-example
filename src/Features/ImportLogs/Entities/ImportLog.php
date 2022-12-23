<?php

namespace App\Features\ImportLogs\Entities;

use App\Features\ImportLogs\DTO\ImportStats;
use DateTimeImmutable;
use SplFileInfo;
use Symfony\Component\Uid\Uuid;

class ImportLog
{
    private string $uuid;

    private DateTimeImmutable $startedAt;

    private DateTimeImmutable $updatedAt;

    const STATE_PROGRESS = 1;
    const STATE_COMPLETE = 2;
    const STATE_ERROR = 3;
    private int $state;

    private string $sourceFilename;

    private ImportStats $stats;

    public function __construct(
        string            $uuid,
        DateTimeImmutable $started,
        DateTimeImmutable $updated,
        int               $state,
        string            $sourceFilename,
        ImportStats       $stats,
    )
    {
        $this->uuid = $uuid;
        $this->startedAt = $started;
        $this->updatedAt = $updated;
        $this->state = $state;
        $this->sourceFilename = $sourceFilename;
        $this->stats = $stats;
    }

    public static function fromFileInfo(SplFileInfo $fileInfo): self
    {
        return new self(
            Uuid::v4(),
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            self::STATE_PROGRESS,
            $fileInfo->getFilename(),
            new ImportStats,
        );
    }

    public static function fromDbRow(array $row): self
    {
        return new self(
            $row['uuid'],
            new DateTimeImmutable($row['started_at']),
            new DateTimeImmutable($row['updated_at']),
            (int)$row['state'],
            $row['source_filename'],
            ImportStats::fromJson($row['stats']),
        );
    }

    public function updateStats(ImportStats $stats): void
    {
        $this->state = self::STATE_PROGRESS;
        $this->stats->add($stats);
        $this->updatedAt = new DateTimeImmutable();
    }

    public function complete(): void
    {
        $this->state = self::STATE_COMPLETE;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function fail(): void
    {
        $this->state = self::STATE_ERROR;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getStartedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function getSourceFilename(): string
    {
        return $this->sourceFilename;
    }

    public function getStats(): ImportStats
    {
        return $this->stats;
    }
}
