<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230306005645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sujet ADD specialites_id INT DEFAULT NULL, ADD statut VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sujet ADD CONSTRAINT FK_2E13599D5AEDDAD9 FOREIGN KEY (specialites_id) REFERENCES specialites (id)');
        $this->addSql('CREATE INDEX IDX_2E13599D5AEDDAD9 ON sujet (specialites_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sujet DROP FOREIGN KEY FK_2E13599D5AEDDAD9');
        $this->addSql('DROP INDEX IDX_2E13599D5AEDDAD9 ON sujet');
        $this->addSql('ALTER TABLE sujet DROP specialites_id, DROP statut');
    }
}
