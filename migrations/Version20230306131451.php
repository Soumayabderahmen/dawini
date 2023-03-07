<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230306131451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article_favorie (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_903535D47294869C (article_id), INDEX IDX_903535D4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article_favorie ADD CONSTRAINT FK_903535D47294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE article_favorie ADD CONSTRAINT FK_903535D4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE article ADD specialites_id INT DEFAULT NULL, ADD date DATE NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E665AEDDAD9 FOREIGN KEY (specialites_id) REFERENCES specialites (id)');
        $this->addSql('CREATE INDEX IDX_23A0E665AEDDAD9 ON article (specialites_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article_favorie DROP FOREIGN KEY FK_903535D47294869C');
        $this->addSql('ALTER TABLE article_favorie DROP FOREIGN KEY FK_903535D4A76ED395');
        $this->addSql('DROP TABLE article_favorie');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E665AEDDAD9');
        $this->addSql('DROP INDEX IDX_23A0E665AEDDAD9 ON article');
        $this->addSql('ALTER TABLE article DROP specialites_id, DROP date');
    }
}
