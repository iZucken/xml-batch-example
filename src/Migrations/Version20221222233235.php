<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221222233235 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create import results/history table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("create table import_logs
(
    uuid            varchar(36)                             not null
        primary key,
    started_at      timestamp default current_timestamp()   not null on update current_timestamp(),
    updated_at      timestamp default '0000-00-00 00:00:00' not null,
    state           int                                     not null,
    source_filename varchar(190)                            not null,
    stats           longtext collate utf8mb4_bin            null,
    constraint stats
        check (json_valid(`stats`))
);
");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("drop table import_logs");
    }
}
