<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230210204202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medecin ADD specialites_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE medecin ADD CONSTRAINT FK_1BDA53C65AEDDAD9 FOREIGN KEY (specialites_id) REFERENCES specialites (id)');
        $this->addSql('CREATE INDEX IDX_1BDA53C65AEDDAD9 ON medecin (specialites_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medecin DROP FOREIGN KEY FK_1BDA53C65AEDDAD9');
        $this->addSql('DROP INDEX IDX_1BDA53C65AEDDAD9 ON medecin');
        $this->addSql('ALTER TABLE medecin DROP specialites_id');
    }
}
