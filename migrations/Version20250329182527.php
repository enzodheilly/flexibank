<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250329182527 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(20) NOT NULL, is_active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_880E0D76E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bank_account (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, iban VARCHAR(20) NOT NULL, account_type VARCHAR(255) NOT NULL, balance NUMERIC(15, 2) NOT NULL, UNIQUE INDEX UNIQ_53A23E0AFAD56E62 (iban), INDEX IDX_53A23E0AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE card_order (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, full_name VARCHAR(255) NOT NULL, birth_date DATE NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(20) NOT NULL, card_type VARCHAR(20) NOT NULL, card_number VARCHAR(19) NOT NULL, ccv VARCHAR(3) NOT NULL, expiration_date DATETIME NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, type VARCHAR(255) DEFAULT NULL, INDEX IDX_CD1D625EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE loan_request (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, duration INT NOT NULL, loan_type VARCHAR(255) NOT NULL, profession VARCHAR(255) NOT NULL, status VARCHAR(255) DEFAULT \'En Attente\' NOT NULL, monthly_payment DOUBLE PRECISION DEFAULT NULL, request_date DATETIME NOT NULL, INDEX IDX_15D801EBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, email VARCHAR(255) NOT NULL, INDEX IDX_7E8585C8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, is_read TINYINT(1) NOT NULL, is_not_read TINYINT(1) NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, email VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, priority VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, submission_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(50) NOT NULL, INDEX IDX_97A0ADA3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transfer (id INT AUTO_INCREMENT NOT NULL, from_account_id INT NOT NULL, to_account_id INT NOT NULL, amount NUMERIC(10, 2) NOT NULL, description VARCHAR(255) NOT NULL, status VARCHAR(50) DEFAULT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', type VARCHAR(255) DEFAULT NULL, INDEX IDX_4034A3C0B0CF99BD (from_account_id), INDEX IDX_4034A3C0BC58BDC7 (to_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', pays_de_residence VARCHAR(100) DEFAULT NULL, profession VARCHAR(100) DEFAULT NULL, adresse_postale VARCHAR(255) DEFAULT NULL, code_postal VARCHAR(20) DEFAULT NULL, ville VARCHAR(100) DEFAULT NULL, numero_telephone VARCHAR(15) DEFAULT NULL, code_client VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, status VARCHAR(20) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649B8C25CF7 (code_client), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bank_account ADD CONSTRAINT FK_53A23E0AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE card_order ADD CONSTRAINT FK_CD1D625EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE loan_request ADD CONSTRAINT FK_15D801EBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE newsletter ADD CONSTRAINT FK_7E8585C8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C0B0CF99BD FOREIGN KEY (from_account_id) REFERENCES bank_account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C0BC58BDC7 FOREIGN KEY (to_account_id) REFERENCES bank_account (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE score');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE score (id INT AUTO_INCREMENT NOT NULL, pseudo VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, jeu VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, score INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE bank_account DROP FOREIGN KEY FK_53A23E0AA76ED395');
        $this->addSql('ALTER TABLE card_order DROP FOREIGN KEY FK_CD1D625EA76ED395');
        $this->addSql('ALTER TABLE loan_request DROP FOREIGN KEY FK_15D801EBA76ED395');
        $this->addSql('ALTER TABLE newsletter DROP FOREIGN KEY FK_7E8585C8A76ED395');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3A76ED395');
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C0B0CF99BD');
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C0BC58BDC7');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE bank_account');
        $this->addSql('DROP TABLE card_order');
        $this->addSql('DROP TABLE loan_request');
        $this->addSql('DROP TABLE newsletter');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE transfer');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
