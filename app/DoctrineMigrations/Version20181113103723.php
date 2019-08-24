<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181113103723 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_payment_epayment ADD transaction_id VARCHAR(255) DEFAULT NULL, ADD decision VARCHAR(255) DEFAULT NULL, ADD message VARCHAR(255) DEFAULT NULL, ADD reason_code VARCHAR(255) DEFAULT NULL, ADD reference_number VARCHAR(255) DEFAULT NULL, ADD card_number VARCHAR(255) DEFAULT NULL, ADD card_expiry_date VARCHAR(255) DEFAULT NULL, ADD card_issuer VARCHAR(255) DEFAULT NULL, ADD card_scheme VARCHAR(255) DEFAULT NULL, ADD card_country VARCHAR(45) DEFAULT NULL, ADD auth_amount NUMERIC(10, 2) DEFAULT NULL, ADD currency VARCHAR(45) DEFAULT NULL, ADD auth_time DATETIME DEFAULT NULL, CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE product CHANGE is_new is_new TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE sku CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_payment_epayment DROP transaction_id, DROP decision, DROP message, DROP reason_code, DROP reference_number, DROP card_number, DROP card_expiry_date, DROP card_issuer, DROP card_scheme, DROP card_country, DROP auth_amount, DROP currency, DROP auth_time, CHANGE status status VARCHAR(45) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE product CHANGE is_new is_new TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE sku CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
