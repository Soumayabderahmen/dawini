<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230306010826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images ADD replay_sujet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AC9592B89 FOREIGN KEY (replay_sujet_id) REFERENCES replay_sujet (id)');
        $this->addSql('CREATE INDEX IDX_E01FBE6AC9592B89 ON images (replay_sujet_id)');
        $this->addSql('ALTER TABLE replay_sujet ADD utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE replay_sujet ADD CONSTRAINT FK_AE613529FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AE613529FB88E14F ON replay_sujet (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AC9592B89');
        $this->addSql('DROP INDEX IDX_E01FBE6AC9592B89 ON images');
        $this->addSql('ALTER TABLE images DROP replay_sujet_id');
        $this->addSql('ALTER TABLE replay_sujet DROP FOREIGN KEY FK_AE613529FB88E14F');
        $this->addSql('DROP INDEX IDX_AE613529FB88E14F ON replay_sujet');
        $this->addSql('ALTER TABLE replay_sujet DROP utilisateur_id');
    }
}
