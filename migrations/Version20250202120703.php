<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250202120703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Recréation de la table author_book avec ses clés étrangères';
    }

    public function up(Schema $schema): void
    {
        // Supprimer la table si elle existe
        $this->addSql('DROP TABLE IF EXISTS author_book');

        // Créer la table author_book
        $this->addSql('CREATE TABLE author_book (
            author_id INT NOT NULL,
            book_id INT NOT NULL,
            INDEX IDX_2F0A2BEEF675F31B (author_id),
            INDEX IDX_2F0A2BEE16A2B381 (book_id),
            PRIMARY KEY(author_id, book_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Ajouter les contraintes de clé étrangère
        $this->addSql('ALTER TABLE author_book ADD CONSTRAINT FK_2F0A2BEEF675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE author_book ADD CONSTRAINT FK_2F0A2BEE16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');

        // Modification de la table comment si nécessaire
        $this->addSql('ALTER TABLE comment CHANGE book_id book_id INT NOT NULL, CHANGE content content LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // Supprime les contraintes de clé étrangère avant de supprimer la table
        $this->addSql('ALTER TABLE author_book DROP FOREIGN KEY FK_2F0A2BEEF675F31B');
        $this->addSql('ALTER TABLE author_book DROP FOREIGN KEY FK_2F0A2BEE16A2B381');

        // Supprime la table author_book
        $this->addSql('DROP TABLE author_book');

        // Restauration de la table comment dans son état précédent
        $this->addSql('ALTER TABLE comment CHANGE book_id book_id INT DEFAULT NULL, CHANGE content content VARCHAR(255) NOT NULL');
    }
}
