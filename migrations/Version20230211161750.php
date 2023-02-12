<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230211161750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement ADD patient_id INT DEFAULT NULL, ADD rendez_vous_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1E6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1E91EF7EAA FOREIGN KEY (rendez_vous_id) REFERENCES rendez_vous (id)');
        $this->addSql('CREATE INDEX IDX_B1DC7A1E6B899279 ON paiement (patient_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B1DC7A1E91EF7EAA ON paiement (rendez_vous_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1E6B899279');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1E91EF7EAA');
        $this->addSql('DROP INDEX IDX_B1DC7A1E6B899279 ON paiement');
        $this->addSql('DROP INDEX UNIQ_B1DC7A1E91EF7EAA ON paiement');
        $this->addSql('ALTER TABLE paiement DROP patient_id, DROP rendez_vous_id');
    }
}
