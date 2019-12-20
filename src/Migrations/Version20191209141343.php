<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191209141343 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, message CLOB NOT NULL, article_id INTEGER NOT NULL)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, title, content, created_at, updated_at, author, nb_views, published FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, author VARCHAR(128) NOT NULL, nb_views INTEGER NOT NULL, published BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO article (id, title, content, created_at, updated_at, author, nb_views, published) SELECT id, title, content, created_at, updated_at, author, nb_views, published FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, title, content, created_at, updated_at, author, nb_views, published FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL COLLATE BINARY, content CLOB DEFAULT NULL COLLATE BINARY, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, author VARCHAR(128) DEFAULT NULL COLLATE BINARY, nb_views INTEGER DEFAULT NULL, published BOOLEAN DEFAULT NULL)');
        $this->addSql('INSERT INTO article (id, title, content, created_at, updated_at, author, nb_views, published) SELECT id, title, content, created_at, updated_at, author, nb_views, published FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
    }
}
