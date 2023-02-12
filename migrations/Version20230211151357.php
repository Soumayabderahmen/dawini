<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230211151357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consulation ADD medecin_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE consulation ADD CONSTRAINT FK_BA23406E4F31A84 FOREIGN KEY (medecin_id) REFERENCES medecin (id)');
        $this->addSql('CREATE INDEX IDX_BA23406E4F31A84 ON consulation (medecin_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consulation DROP FOREIGN KEY FK_BA23406E4F31A84');
        $this->addSql('DROP INDEX IDX_BA23406E4F31A84 ON consulation');
        $this->addSql('ALTER TABLE consulation DROP medecin_id');
    }
}
