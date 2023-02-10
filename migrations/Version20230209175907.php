<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230209175907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE specialites (id INT AUTO_INCREMENT NOT NULL, admin_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_F78AEBD1642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE specialites_patient (specialites_id INT NOT NULL, patient_id INT NOT NULL, INDEX IDX_E0BD090A5AEDDAD9 (specialites_id), INDEX IDX_E0BD090A6B899279 (patient_id), PRIMARY KEY(specialites_id, patient_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE specialites ADD CONSTRAINT FK_F78AEBD1642B8210 FOREIGN KEY (admin_id) REFERENCES admin (id)');
        $this->addSql('ALTER TABLE specialites_patient ADD CONSTRAINT FK_E0BD090A5AEDDAD9 FOREIGN KEY (specialites_id) REFERENCES specialites (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE specialites_patient ADD CONSTRAINT FK_E0BD090A6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE medecin ADD specialites_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE medecin ADD CONSTRAINT FK_1BDA53C65AEDDAD9 FOREIGN KEY (specialites_id) REFERENCES specialites (id)');
        $this->addSql('CREATE INDEX IDX_1BDA53C65AEDDAD9 ON medecin (specialites_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medecin DROP FOREIGN KEY FK_1BDA53C65AEDDAD9');
        $this->addSql('ALTER TABLE specialites DROP FOREIGN KEY FK_F78AEBD1642B8210');
        $this->addSql('ALTER TABLE specialites_patient DROP FOREIGN KEY FK_E0BD090A5AEDDAD9');
        $this->addSql('ALTER TABLE specialites_patient DROP FOREIGN KEY FK_E0BD090A6B899279');
        $this->addSql('DROP TABLE specialites');
        $this->addSql('DROP TABLE specialites_patient');
        $this->addSql('DROP INDEX IDX_1BDA53C65AEDDAD9 ON medecin');
        $this->addSql('ALTER TABLE medecin DROP specialites_id');
    }
}
