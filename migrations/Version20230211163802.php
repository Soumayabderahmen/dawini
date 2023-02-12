<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230211163802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE diagnostique (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, consultation_id INT DEFAULT NULL, date DATE NOT NULL, symptome VARCHAR(255) NOT NULL, resultat_test VARCHAR(255) NOT NULL, diag_final VARCHAR(255) NOT NULL, INDEX IDX_38C9AFE96B899279 (patient_id), INDEX IDX_38C9AFE962FF6CDF (consultation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE diagnostique ADD CONSTRAINT FK_38C9AFE96B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE diagnostique ADD CONSTRAINT FK_38C9AFE962FF6CDF FOREIGN KEY (consultation_id) REFERENCES consulation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE diagnostique DROP FOREIGN KEY FK_38C9AFE96B899279');
        $this->addSql('ALTER TABLE diagnostique DROP FOREIGN KEY FK_38C9AFE962FF6CDF');
        $this->addSql('DROP TABLE diagnostique');
    }
}
