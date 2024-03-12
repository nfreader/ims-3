<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240311235518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds the `active` column to the incident table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE `incident` ADD `active` BOOL  NOT NULL  DEFAULT '1' AFTER `role`;");
    }

    public function down(Schema $schema): void
    {

    }

    public function isTransactional(): bool
    {
        return false;
    }
}
