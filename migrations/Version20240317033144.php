<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240317033144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Removes `comment_edit.diff` since we can handle it in PHP instead. Adds roles to comments and comment edits. Changes `event`.`desc` to `event`.`event_text`';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE `comment_edit` DROP `diff`;");
        $this->addSql("ALTER TABLE `comment_edit` ADD `role` INT(11)  UNSIGNED  NULL  AFTER `editor`;");
        $this->addSql("ALTER TABLE `comment` ADD `editor_role` INT(11)  UNSIGNED  NULL  DEFAULT NULL  AFTER `role`;");
        $this->addSql("ALTER TABLE `comment` ADD `role` INT(11)  UNSIGNED  NULL  DEFAULT NULL  AFTER `author`;");
        $this->addSql("ALTER TABLE `event` CHANGE `desc` `event_text` LONGTEXT  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;");
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
