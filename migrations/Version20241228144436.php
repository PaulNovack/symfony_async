<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241228144436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD first_name VARCHAR(255) NOT NULL, ADD last_name VARCHAR(255) NOT NULL, ADD password VARCHAR(255) NOT NULL, ADD roles JSON NOT NULL, DROP name, DROP created_at, CHANGE email email VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD name VARCHAR(100) NOT NULL, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP, DROP first_name, DROP last_name, DROP password, DROP roles, CHANGE email email VARCHAR(100) NOT NULL');
    }
}
