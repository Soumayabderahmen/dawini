<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223225529 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assistant ADD medecin_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE assistant ADD CONSTRAINT FK_C2997CD14F31A84 FOREIGN KEY (medecin_id) REFERENCES medecin (id)');
        $this->addSql('CREATE INDEX IDX_C2997CD14F31A84 ON assistant (medecin_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assistant DROP FOREIGN KEY FK_C2997CD14F31A84');
        $this->addSql('DROP INDEX IDX_C2997CD14F31A84 ON assistant');
        $this->addSql('ALTER TABLE assistant DROP medecin_id');
    }
}
