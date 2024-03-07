<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240307215823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds `role` column to event table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE `event` ADD `role` INT(11) UNSIGNED NULL  DEFAULT NULL AFTER `creator`;");
        $this->addSql("ALTER TABLE `event` ADD CONSTRAINT event_idbfk_3 FOREIGN KEY (`role`) REFERENCES `role` (id)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE `event` DROP FOREIGN KEY `event_idbfk_3`;");
        $this->addSql("ALTER TABLE `event` DROP `role`;");
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
