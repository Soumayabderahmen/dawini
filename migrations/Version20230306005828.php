<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230306005828 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sujet_like (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, sujet_id INT DEFAULT NULL, value INT NOT NULL, INDEX IDX_7BB28724A76ED395 (user_id), INDEX IDX_7BB287247C4D497E (sujet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sujet_like ADD CONSTRAINT FK_7BB28724A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sujet_like ADD CONSTRAINT FK_7BB287247C4D497E FOREIGN KEY (sujet_id) REFERENCES sujet (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sujet_like DROP FOREIGN KEY FK_7BB28724A76ED395');
        $this->addSql('ALTER TABLE sujet_like DROP FOREIGN KEY FK_7BB287247C4D497E');
        $this->addSql('DROP TABLE sujet_like');
    }
}
