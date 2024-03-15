<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240315151152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Removes the `incident_agency` and `user_agency` tables which are no longer needed';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("DROP TABLE `incident_agency`;");
        $this->addSql("DROP TABLE `user_agency`;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }

    public function isTransactional(): bool
    {
        return false;
    }
}
