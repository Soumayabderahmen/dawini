<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230211150055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE certificat (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE consulation ADD certificat_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE consulation ADD CONSTRAINT FK_BA23406EFA55BACF FOREIGN KEY (certificat_id) REFERENCES certificat (id)');
        $this->addSql('CREATE INDEX IDX_BA23406EFA55BACF ON consulation (certificat_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consulation DROP FOREIGN KEY FK_BA23406EFA55BACF');
        $this->addSql('DROP TABLE certificat');
        $this->addSql('DROP INDEX IDX_BA23406EFA55BACF ON consulation');
        $this->addSql('ALTER TABLE consulation DROP certificat_id');
    }
}
