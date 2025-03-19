<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250316120922 extends AbstractMigration
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
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY transfer_ibfk_1');
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY transfer_ibfk_2');
        $this->addSql('ALTER TABLE transfer CHANGE date date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C0B0CF99BD FOREIGN KEY (from_account_id) REFERENCES bank_account (id)');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C0BC58BDC7 FOREIGN KEY (to_account_id) REFERENCES bank_account (id)');
        $this->addSql('ALTER TABLE transfer RENAME INDEX from_account_id TO IDX_4034A3C0B0CF99BD');
        $this->addSql('ALTER TABLE transfer RENAME INDEX to_account_id TO IDX_4034A3C0BC58BDC7');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY user_ibfk_1');
        $this->addSql('ALTER TABLE user RENAME INDEX email TO UNIQ_8D93D649E7927C74');
        $this->addSql('ALTER TABLE user RENAME INDEX code_client TO UNIQ_8D93D649B8C25CF7');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_order CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE card_order RENAME INDEX idx_cd1d625ea76ed395 TO FK_CD1D625EA76ED395');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT user_ibfk_1 FOREIGN KEY (id) REFERENCES bank_account (user_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_8d93d649b8c25cf7 TO code_client');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_8d93d649e7927c74 TO email');
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C0B0CF99BD');
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C0BC58BDC7');
        $this->addSql('ALTER TABLE transfer CHANGE date date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT transfer_ibfk_1 FOREIGN KEY (from_account_id) REFERENCES bank_account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT transfer_ibfk_2 FOREIGN KEY (to_account_id) REFERENCES bank_account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transfer RENAME INDEX idx_4034a3c0bc58bdc7 TO to_account_id');
        $this->addSql('ALTER TABLE transfer RENAME INDEX idx_4034a3c0b0cf99bd TO from_account_id');
    }
}
