<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260607112000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add token (BINARY(16)) and expires_at columns to calendars table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE calendars ADD COLUMN token BINARY(16) NOT NULL AFTER id');
        $this->addSql('ALTER TABLE calendars ADD UNIQUE INDEX idx_calendars_token (token)');
        $this->addSql('ALTER TABLE calendars ADD COLUMN expires_at DATETIME NOT NULL AFTER created_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE calendars DROP INDEX idx_calendars_token');
        $this->addSql('ALTER TABLE calendars DROP COLUMN token');
        $this->addSql('ALTER TABLE calendars DROP COLUMN expires_at');
    }
}
