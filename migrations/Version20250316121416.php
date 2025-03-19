<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250316121416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_order CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE card_order RENAME INDEX fk_cd1d625ea76ed395 TO IDX_CD1D625EA76ED395');
        $this->addSql('ALTER TABLE transfer RENAME INDEX fk_4034a3c0b0cf99bd TO IDX_4034A3C0B0CF99BD');
        $this->addSql('ALTER TABLE transfer RENAME INDEX fk_4034a3c0bc58bdc7 TO IDX_4034A3C0BC58BDC7');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_order CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE card_order RENAME INDEX idx_cd1d625ea76ed395 TO FK_CD1D625EA76ED395');
        $this->addSql('ALTER TABLE transfer RENAME INDEX idx_4034a3c0bc58bdc7 TO FK_4034A3C0BC58BDC7');
        $this->addSql('ALTER TABLE transfer RENAME INDEX idx_4034a3c0b0cf99bd TO FK_4034A3C0B0CF99BD');
    }
}
