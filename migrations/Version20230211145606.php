<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230211145606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ordonnance (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE consulation ADD ordonnance_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE consulation ADD CONSTRAINT FK_BA23406E2BF23B8F FOREIGN KEY (ordonnance_id) REFERENCES ordonnance (id)');
        $this->addSql('CREATE INDEX IDX_BA23406E2BF23B8F ON consulation (ordonnance_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consulation DROP FOREIGN KEY FK_BA23406E2BF23B8F');
        $this->addSql('DROP TABLE ordonnance');
        $this->addSql('DROP INDEX IDX_BA23406E2BF23B8F ON consulation');
        $this->addSql('ALTER TABLE consulation DROP ordonnance_id');
    }
}
