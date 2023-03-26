<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230307060121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consulation DROP FOREIGN KEY FK_BA23406E2BF23B8F');
        $this->addSql('DROP INDEX IDX_BA23406E2BF23B8F ON consulation');
        $this->addSql('ALTER TABLE consulation ADD heuredebut DATETIME NOT NULL, ADD heurefin DATETIME NOT NULL, ADD url_consultation VARCHAR(255) NOT NULL, ADD est_termine VARCHAR(255) DEFAULT NULL, DROP ordonnance_id, DROP description');
        $this->addSql('ALTER TABLE diagnostique ADD dossiers_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE diagnostique ADD CONSTRAINT FK_38C9AFE9651855E8 FOREIGN KEY (dossiers_id) REFERENCES dossier (id)');
        $this->addSql('CREATE INDEX IDX_38C9AFE9651855E8 ON diagnostique (dossiers_id)');
        $this->addSql('ALTER TABLE ordonnance ADD consulation_id INT DEFAULT NULL, ADD date DATE NOT NULL, ADD image VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE ordonnance ADD CONSTRAINT FK_924B326C76161DD7 FOREIGN KEY (consulation_id) REFERENCES consulation (id)');
        $this->addSql('CREATE INDEX IDX_924B326C76161DD7 ON ordonnance (consulation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consulation ADD ordonnance_id INT DEFAULT NULL, ADD description LONGTEXT NOT NULL, DROP heuredebut, DROP heurefin, DROP url_consultation, DROP est_termine');
        $this->addSql('ALTER TABLE consulation ADD CONSTRAINT FK_BA23406E2BF23B8F FOREIGN KEY (ordonnance_id) REFERENCES ordonnance (id)');
        $this->addSql('CREATE INDEX IDX_BA23406E2BF23B8F ON consulation (ordonnance_id)');
        $this->addSql('ALTER TABLE diagnostique DROP FOREIGN KEY FK_38C9AFE9651855E8');
        $this->addSql('DROP INDEX IDX_38C9AFE9651855E8 ON diagnostique');
        $this->addSql('ALTER TABLE diagnostique DROP dossiers_id');
        $this->addSql('ALTER TABLE ordonnance DROP FOREIGN KEY FK_924B326C76161DD7');
        $this->addSql('DROP INDEX IDX_924B326C76161DD7 ON ordonnance');
        $this->addSql('ALTER TABLE ordonnance DROP consulation_id, DROP date, DROP image');
    }
}
