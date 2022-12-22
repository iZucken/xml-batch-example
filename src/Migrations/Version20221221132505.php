<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221221132505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create products catalog table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("create table products
(
    id                          int auto_increment
        primary key,
    name                        varchar(190)   not null,
    weight                      varchar(190)   not null,
    category                    varchar(190)   not null,
    absolute_weight             decimal(26, 6) not null,
    description_common          text           null,
    description_for_ozon        text           null,
    description_for_wildberries text           null,
    constraint stupid_table_name_uindex
        unique (name)
);");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("drop table products");
    }
}
