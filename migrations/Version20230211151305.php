<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230211151305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consulation ADD patients_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE consulation ADD CONSTRAINT FK_BA23406ECEC3FD2F FOREIGN KEY (patients_id) REFERENCES patient (id)');
        $this->addSql('CREATE INDEX IDX_BA23406ECEC3FD2F ON consulation (patients_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consulation DROP FOREIGN KEY FK_BA23406ECEC3FD2F');
        $this->addSql('DROP INDEX IDX_BA23406ECEC3FD2F ON consulation');
        $this->addSql('ALTER TABLE consulation DROP patients_id');
    }
}
