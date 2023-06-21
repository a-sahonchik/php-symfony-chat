<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230621184821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat_message ADD author_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', DROP author_name');
        $this->addSql('ALTER TABLE chat_message ADD CONSTRAINT FK_FAB3FC16F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_FAB3FC16F675F31B ON chat_message (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat_message DROP FOREIGN KEY FK_FAB3FC16F675F31B');
        $this->addSql('DROP INDEX IDX_FAB3FC16F675F31B ON chat_message');
        $this->addSql('ALTER TABLE chat_message ADD author_name VARCHAR(255) NOT NULL, DROP author_id');
    }
}
