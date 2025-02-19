<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250107084444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        // Creating Book table
        $this->addSql('CREATE TABLE book (
        id INT AUTO_INCREMENT NOT NULL, 
        title VARCHAR(255) NOT NULL, 
        author VARCHAR(50) NOT NULL, 
        description VARCHAR(1024) DEFAULT NULL, 
        year SMALLINT DEFAULT NULL, 
        genre VARCHAR(50) DEFAULT NULL, 
        page_count INT DEFAULT NULL, 
        cover_image VARCHAR(255) DEFAULT NULL, 
        created_at DATETIME NOT NULL, 
        updated_at DATETIME DEFAULT NULL, 
        is_available TINYINT(1) NULL, 
        added_by_id INT NOT NULL, 
        INDEX IDX_CBE5A33155B127A4 (added_by_id), 
        PRIMARY KEY(id)
    ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Creating Review table
        $this->addSql('CREATE TABLE review (
        id INT AUTO_INCREMENT NOT NULL, 
        rating SMALLINT DEFAULT NULL, 
        comment LONGTEXT DEFAULT NULL, 
        created_at DATETIME NOT NULL, 
        book_id INT DEFAULT NULL, 
        user_id INT NOT NULL, 
        INDEX IDX_794381C616A2B381 (book_id), 
        UNIQUE INDEX UNIQ_794381C6A76ED395 (user_id, book_id), 
        PRIMARY KEY(id)
    ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Creating User table
        $this->addSql('CREATE TABLE user (
        id INT AUTO_INCREMENT NOT NULL, 
        email VARCHAR(50) NOT NULL, 
        password VARCHAR(255) NOT NULL, 
        name VARCHAR(50) NOT NULL, 
        roles JSON NOT NULL, 
        registered_at DATETIME NOT NULL, 
        profile_picture VARCHAR(255) DEFAULT NULL, 
        PRIMARY KEY(id)
    ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Creating Messenger Messages table
        $this->addSql('CREATE TABLE messenger_messages (
        id BIGINT AUTO_INCREMENT NOT NULL, 
        body LONGTEXT NOT NULL, 
        headers LONGTEXT NOT NULL, 
        queue_name VARCHAR(190) NOT NULL, 
        created_at DATETIME NOT NULL, 
        available_at DATETIME NOT NULL, 
        delivered_at DATETIME DEFAULT NULL, 
        INDEX IDX_75EA56E0FB7336F0 (queue_name), 
        INDEX IDX_75EA56E0E3BD61CE (available_at), 
        INDEX IDX_75EA56E016BA31DB (delivered_at), 
        PRIMARY KEY(id)
    ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Adding foreign keys
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A33155B127A4 FOREIGN KEY (added_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C616A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');

        // Creating Doctrine Migrations table to track migrations
        $this->addSql('CREATE TABLE doctrine_migration_versions (
        version VARCHAR(191) NOT NULL, 
        executed_at DATETIME DEFAULT NULL, 
        execution_time INTEGER DEFAULT NULL, 
        PRIMARY KEY(version)
    ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // Drop the foreign key constraints
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C616A2B381');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A33155B127A4');

        // Drop the tables
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');

        // Drop the doctrine_migration_versions table
        $this->addSql('DROP TABLE doctrine_migration_versions');
    }
}
