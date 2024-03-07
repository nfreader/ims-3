<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240307164532 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );


        $this->addSql('CREATE TABLE user (id INT UNSIGNED AUTO_INCREMENT NOT NULL, firstName VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, lastName VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, created_ip INT NOT NULL, status TINYINT(1) DEFAULT 0 NOT NULL, is_admin TINYINT(1) DEFAULT 0 NOT NULL, UNIQUE INDEX email (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('CREATE TABLE incident (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, creator INT UNSIGNED NOT NULL, created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, role INT UNSIGNED DEFAULT NULL, INDEX creator (creator), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE incident ADD CONSTRAINT incident_ibfk_1 FOREIGN KEY (creator) REFERENCES user (id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('CREATE TABLE event (id INT UNSIGNED AUTO_INCREMENT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, `desc` LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, severity VARCHAR(16) CHARACTER SET utf8mb4 DEFAULT \'INFORMATIONAL\' NOT NULL COLLATE `utf8mb4_general_ci`, incident INT UNSIGNED NOT NULL, creator INT UNSIGNED NOT NULL, created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, edited DATETIME DEFAULT NULL, editor INT UNSIGNED DEFAULT NULL, INDEX creator (creator), INDEX incident (incident), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT event_ibfk_1 FOREIGN KEY (creator) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT event_ibfk_2 FOREIGN KEY (incident) REFERENCES incident (id)');

        $this->addSql('CREATE TABLE comment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, text LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, incident INT UNSIGNED NOT NULL, event INT UNSIGNED NOT NULL, author INT UNSIGNED NOT NULL, created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated DATETIME DEFAULT NULL, editor INT UNSIGNED DEFAULT NULL, action VARCHAR(12) CHARACTER SET utf8mb4 DEFAULT \'comment\' NOT NULL COLLATE `utf8mb4_general_ci`, INDEX incident (incident), INDEX event (event), INDEX author (author), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT comment_ibfk_1 FOREIGN KEY (incident) REFERENCES incident (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT comment_ibfk_2 FOREIGN KEY (event) REFERENCES event (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT comment_ibfk_3 FOREIGN KEY (author) REFERENCES user (id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('CREATE TABLE attachment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, fileName VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, mimeType VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, uploader INT UNSIGNED NOT NULL, uploaded DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, incident INT UNSIGNED NOT NULL, event INT UNSIGNED DEFAULT NULL, comment INT UNSIGNED DEFAULT NULL, INDEX uploader (uploader), INDEX incident (incident), INDEX event (event), INDEX comment (comment), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE attachment ADD CONSTRAINT attachment_ibfk_3 FOREIGN KEY (event) REFERENCES event (id)');
        $this->addSql('ALTER TABLE attachment ADD CONSTRAINT attachment_ibfk_1 FOREIGN KEY (uploader) REFERENCES user (id)');
        $this->addSql('ALTER TABLE attachment ADD CONSTRAINT attachment_ibfk_4 FOREIGN KEY (comment) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE attachment ADD CONSTRAINT attachment_ibfk_2 FOREIGN KEY (incident) REFERENCES incident (id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('CREATE TABLE user_role (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user INT UNSIGNED NOT NULL, role INT UNSIGNED NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, created DATETIME DEFAULT CURRENT_TIMESTAMP, UNIQUE INDEX user (user, role), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('CREATE TABLE user_agency (id INT UNSIGNED AUTO_INCREMENT NOT NULL, target INT UNSIGNED NOT NULL, agency INT UNSIGNED NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, creator INT UNSIGNED NOT NULL, created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated DATETIME DEFAULT NULL, status TINYINT(1) DEFAULT 1 NOT NULL, UNIQUE INDEX user (target, agency), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('CREATE TABLE incident_permission_flags (id INT UNSIGNED AUTO_INCREMENT NOT NULL, incident INT UNSIGNED NOT NULL, flags INT UNSIGNED DEFAULT 0 NOT NULL, type VARCHAR(8) CHARACTER SET utf8mb4 DEFAULT \'role\' NOT NULL COLLATE `utf8mb4_general_ci`, created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, target INT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('CREATE TABLE agency (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, logo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, fullname VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, location VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, active TINYINT(1) DEFAULT 1 NOT NULL, updated DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('CREATE TABLE incident_agency (agency INT UNSIGNED NOT NULL, incident INT UNSIGNED NOT NULL, status TINYINT(1) DEFAULT 0 NOT NULL, UNIQUE INDEX agency (agency, incident), PRIMARY KEY(incident, agency)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('CREATE TABLE role (id INT UNSIGNED AUTO_INCREMENT NOT NULL, agency INT UNSIGNED NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );


        $this->addSql('CREATE TABLE comment_edit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, comment INT UNSIGNED NOT NULL, previous LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, current LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, edited DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, editor INT UNSIGNED NOT NULL, diff LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('DROP TABLE attachment');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('DROP TABLE user_role');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('DROP TABLE user_agency');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('DROP TABLE incident');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('DROP TABLE comment');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('DROP TABLE incident_permission_flags');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('DROP TABLE agency');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('DROP TABLE incident_agency');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('DROP TABLE role');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('DROP TABLE user');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('DROP TABLE comment_edit');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1060Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1060Platform'."
        );

        $this->addSql('DROP TABLE event');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
