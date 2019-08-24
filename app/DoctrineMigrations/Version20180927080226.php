<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180927080226 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE customer_payment_bank_transfer (id INT AUTO_INCREMENT NOT NULL, bank_account_id INT DEFAULT NULL, order_id INT NOT NULL, order_number VARCHAR(45) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, phone VARCHAR(45) DEFAULT NULL, amount NUMERIC(10, 2) NOT NULL, attach_file VARCHAR(255) DEFAULT NULL, date_transfer DATE DEFAULT NULL, time_transfer TIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_DA1C282612CB990C (bank_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer_payment_bank_transfer ADD CONSTRAINT FK_DA1C282612CB990C FOREIGN KEY (bank_account_id) REFERENCES bank_account (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE customer_payment_bank_transfer');
    }
}
