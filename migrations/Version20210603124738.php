<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210603124738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adding cascade persitence and removal to user and ';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_D7D174C9FB8E54CD');
        $this->addSql('CREATE TEMPORARY TABLE __temp__wish AS SELECT id, wishlist_id, name, description, price, url, image, is_offered FROM wish');
        $this->addSql('DROP TABLE wish');
        $this->addSql('CREATE TABLE wish (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, wishlist_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, description VARCHAR(255) DEFAULT NULL COLLATE BINARY, price NUMERIC(10, 2) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL COLLATE BINARY, image VARCHAR(255) DEFAULT NULL COLLATE BINARY, is_offered BOOLEAN NOT NULL, CONSTRAINT FK_D7D174C9FB8E54CD FOREIGN KEY (wishlist_id) REFERENCES "wishlist" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO wish (id, wishlist_id, name, description, price, url, image, is_offered) SELECT id, wishlist_id, name, description, price, url, image, is_offered FROM __temp__wish');
        $this->addSql('DROP TABLE __temp__wish');
        $this->addSql('CREATE INDEX IDX_D7D174C9FB8E54CD ON wish (wishlist_id)');
        $this->addSql('DROP INDEX IDX_9CE12A31A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__wishlist AS SELECT id, user_id, title, description FROM wishlist');
        $this->addSql('DROP TABLE wishlist');
        $this->addSql('CREATE TABLE wishlist (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL COLLATE BINARY, description VARCHAR(255) DEFAULT NULL COLLATE BINARY, CONSTRAINT FK_9CE12A31A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO wishlist (id, user_id, title, description) SELECT id, user_id, title, description FROM __temp__wishlist');
        $this->addSql('DROP TABLE __temp__wishlist');
        $this->addSql('CREATE INDEX IDX_9CE12A31A76ED395 ON wishlist (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_D7D174C9FB8E54CD');
        $this->addSql('CREATE TEMPORARY TABLE __temp__wish AS SELECT id, wishlist_id, name, description, price, url, image, is_offered FROM "wish"');
        $this->addSql('DROP TABLE "wish"');
        $this->addSql('CREATE TABLE "wish" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, wishlist_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, price NUMERIC(10, 2) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, is_offered BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO "wish" (id, wishlist_id, name, description, price, url, image, is_offered) SELECT id, wishlist_id, name, description, price, url, image, is_offered FROM __temp__wish');
        $this->addSql('DROP TABLE __temp__wish');
        $this->addSql('CREATE INDEX IDX_D7D174C9FB8E54CD ON "wish" (wishlist_id)');
        $this->addSql('DROP INDEX IDX_9CE12A31A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__wishlist AS SELECT id, user_id, title, description FROM "wishlist"');
        $this->addSql('DROP TABLE "wishlist"');
        $this->addSql('CREATE TABLE "wishlist" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO "wishlist" (id, user_id, title, description) SELECT id, user_id, title, description FROM __temp__wishlist');
        $this->addSql('DROP TABLE __temp__wishlist');
        $this->addSql('CREATE INDEX IDX_9CE12A31A76ED395 ON "wishlist" (user_id)');
    }
}
